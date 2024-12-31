<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\bugfix;

use Exception;
use Generator;
use Yii;
use app\models\Agent;
use app\models\AgentVariable3;
use app\models\Battle3;
use app\models\BattleAgentVariable3;
use app\models\BattlePlayer3;
use yii\base\Action;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Transaction;

use function array_filter;
use function array_map;
use function array_values;
use function assert;
use function fprintf;
use function fwrite;
use function ltrim;
use function str_starts_with;
use function version_compare;
use function vfprintf;

use const SORT_ASC;
use const STDERR;

final class S3sKillAssistAction extends Action
{
    /**
     * s3s < 0.1.3 において、SplatNet3 のデータ解釈に誤りがあり (s3s#30)、
     * "kill" フィールドに kill + assist が格納され、
     * 過大な数字を記録していた問題に修正する。
     *
     * 1. 当該 s3s の agent_id の一覧を取得する
     * 2．当該 s3s によって記録されたレコードを探す
     * 3. そのレコードが既に修正済みでないか調べる
     *   - agent_variable に {"s3s issue 30": "Fixed by stat.ink API endpoint"} が記録されている
     *     ならば、そのレコードは保存時に修正済み (#1089 による）
     *   - agent_variable に {"s3s issue 30": "Fixed by stat.ink"} が記録されているならば、
     *     このスクリプトによって修正済み
     * 4. 修正の必要があれば、battle3 および battle_player3 を修正して commit する
     */
    public function run(): int
    {
        if (!$agentIds = $this->getTargetS3sIds()) {
            fwrite(STDERR, "No s3s found\n");
            return ExitCode::OK;
        }

        $db = Yii::$app->db;
        assert($db instanceof Connection);

        $failed = false;
        foreach ($this->enumTargetBattles($agentIds) as $battle) {
            // vfprintf(STDERR, "[Debug] to be fixed (id = %d)\n", [
            //     $battle->id,
            // ]);
            if (
                $db->transaction(
                    fn (Connection $db): bool => $this->updateBattleRelation(
                        $battle,
                        $db->transaction,
                    ),
                )
            ) {
                fprintf(STDERR, "[Info] Updated %d\n", $battle->id);
            } else {
                $failed = true;
            }
        }

        return $failed ? ExitCode::UNSPECIFIED_ERROR : ExitCode::OK;
    }

    /**
     * @return int[]
     */
    private function getTargetS3sIds(): array
    {
        $models = Agent::find()
            ->andWhere(['name' => 's3s'])
            ->andWhere(['~', 'version', '^v?0\.'])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        return array_values(
            array_filter(
                array_map(
                    function (Agent $model): ?int {
                        if (
                            version_compare(
                                ltrim($model->version, 'v'),
                                '0.1.3',
                                '<',
                            )
                        ) {
                            vfprintf(STDERR, "[Info] %s/%s id=%d\n", [
                                $model->name,
                                $model->version,
                                $model->id,
                            ]);
                            return $model->id;
                        }

                        return null;
                    },
                    $models,
                ),
                fn (?int $id): bool => $id !== null,
            ),
        );
    }

    /**
     * @return Generator<Battle3>
     */
    private function enumTargetBattles(array $agentIds): Generator
    {
        $query = Battle3::find()
            ->andWhere(['and',
                ['agent_id' => $agentIds],
                ['is_deleted' => false],
                ['not', ['kill' => null]],
                ['not', ['assist' => null]],
                '[[kill]] >= [[assist]]',
            ])
            ->with(['battlePlayer3s', 'variables'])
            ->orderBy(['id' => SORT_ASC]);

        foreach ($query->each() as $model) {
            if (!$this->isAlreadyFixed($model)) {
                yield $model;
            }
        }
    }

    private function isAlreadyFixed(Battle3 $model): bool
    {
        foreach ($model->variables as $var) {
            if (
                $var->key === 's3s issue 30' &&
                str_starts_with($var->value, 'Fixed')
            ) {
                // vfprintf(STDERR, "[Debug] %d is already fixed\n", [
                //     $model->id,
                // ]);
                return true;
            }
        }

        return false;
    }

    private function updateBattleRelation(Battle3 $battle, Transaction $transaction): bool
    {
        if (
            $this->updateBattle($battle) &&
            $this->updatePlayers($battle->battlePlayer3s) &&
            $this->markUpdated($battle)
        ) {
            return true;
        }

        $transaction->rollBack();
        return false;
    }

    private function updateBattle(Battle3 $battle): bool
    {
        if ($battle->kill < $battle->assist) {
            vfprintf(STDERR, "[Warn] Battle3 id=%d, kill=%d, assist=%d\n", [
                $battle->id,
                $battle->kill,
                $battle->assist,
            ]);
        }

        $battle->kill_or_assist = $battle->kill;
        $battle->kill = $battle->kill - $battle->assist;
        return $battle->save();
    }

    private function updatePlayers(array $players): bool
    {
        foreach ($players as $player) {
            if (!$this->updatePlayer($player)) {
                return false;
            }
        }

        return true;
    }

    private function updatePlayer(BattlePlayer3 $player): bool
    {
        if ($player->kill < $player->assist) {
            vfprintf(STDERR, "[Warn] BattlePlayer3 battle_id=%d, kill=%d, assist=%d\n", [
                $player->battle_id,
                $player->kill,
                $player->assist,
            ]);
        }

        $player->kill_or_assist = $player->kill;
        $player->kill = $player->kill - $player->assist;
        return $player->save();
    }

    private function markUpdated(Battle3 $battle): bool
    {
        $model = Yii::createObject([
            'class' => BattleAgentVariable3::class,
            'battle_id' => $battle->id,
            'variable_id' => $this->getFixedAgentVariableId(),
        ]);
        return $model->save();
    }

    private function getFixedAgentVariableId(): int
    {
        static $cache = null;
        if ($cache === null) {
            $cache = $this->createOrFindFixAgentVariableId();
        }
        return $cache;
    }

    private function createOrFindFixAgentVariableId(): int
    {
        $model = AgentVariable3::findOne([
            'key' => 's3s issue 30',
            'value' => 'Fixed by stat.ink',
        ]);
        if ($model) {
            return $model->id;
        }

        $model = Yii::createObject([
            'class' => AgentVariable3::class,
            'key' => 's3s issue 30',
            'value' => 'Fixed by stat.ink',
        ]);
        if (!$model->save()) {
            throw new Exception();
        }

        return $model->id;
    }
}

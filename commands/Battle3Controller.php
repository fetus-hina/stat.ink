<?php

/**
 * @copyright Copyright (C) 2024-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use Yii;
use app\components\helpers\Battle3Helper;
use app\models\Ability3;
use app\models\Battle3;
use app\models\BattlePlayer3;
use app\models\Lobby3;
use yii\console\Controller;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

use function filter_var;
use function fwrite;
use function is_int;
use function usort;
use function vsprintf;

use const FILTER_VALIDATE_INT;
use const JSON_NUMERIC_CHECK;
use const JSON_PRETTY_PRINT;
use const JSON_THROW_ON_ERROR;
use const JSON_UNESCAPED_SLASHES;
use const JSON_UNESCAPED_UNICODE;
use const SORT_ASC;
use const STDERR;

final class Battle3Controller extends Controller
{
    public function actionCalcGearPowers(string $id): int
    {
        $isUuid = !is_int(filter_var($id, FILTER_VALIDATE_INT));
        $battle = Battle3::find()
            ->with(Battle3Helper::getRelationsForApiResponse(false))
            ->andWhere(['and',
                ['is_deleted' => false],
                $isUuid
                    ? ['uuid' => $id]
                    : ['id' => filter_var($id, FILTER_VALIDATE_INT)],
            ])
            ->limit(1)
            ->one();
        if (!$battle) {
            fwrite(STDERR, "Battle not found\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        $players = $battle->battlePlayer3s ?: $battle->battleTricolorPlayer3s;
        if (!$players) {
            fwrite(STDERR, "No players\n");

            return ExitCode::UNSPECIFIED_ERROR;
        }

        usort($players, fn ($a, $b) => $a->id <=> $b->id);

        $results = [];
        foreach ($players as $player) {
            $results[vsprintf('%s #%s (%d)', [
                $player->name,
                $player->number,
                $player->id,
            ])] = Battle3Helper::calcGPs($player);
        }

        echo Json::encode(
            $results,
            JSON_NUMERIC_CHECK | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        ) . "\n";

        return 0;
    }

    public function actionCreateMissingGearPowersData(): int
    {
        $exclude1 = (new Query())
            ->select('player_id')
            ->from('{{%battle_player_gear_power3}}')
            ->groupBy('player_id');
        // $exclude2 = (new Query())
        //     ->select('player_id')
        //     ->from('{{%battle_tricolor_player_gear_power3}}')
        //     ->groupBy('player_id');

        $query = BattlePlayer3::find()
            ->innerJoinWith(['battle'], false)
            ->innerJoin(
                ['clothing' => '{{%gear_configuration3}}'],
                '{{%battle_player3}}.[[clothing_id]] = {{clothing}}.[[id]]',
            )
            ->innerJoin(
                ['headgear' => '{{%gear_configuration3}}'],
                '{{%battle_player3}}.[[headgear_id]] = {{headgear}}.[[id]]',
            )
            ->innerJoin(
                ['shoes' => '{{%gear_configuration3}}'],
                '{{%battle_player3}}.[[shoes_id]] = {{shoes}}.[[id]]',
            )
            ->leftJoin(
                ['exclude1' => $exclude1],
                '{{%battle_player3}}.[[id]] = {{exclude1}}.[[player_id]]',
            )
            // ->leftJoin(
            //     ['exclude2' => $exclude2],
            //     '{{%battle_player3}}.[[id]] = {{exclude2}}.[[player_id]]',
            // )
            ->andWhere(['and',
                [
                    '{{%battle3}}.[[has_disconnect]]' => false,
                    '{{%battle3}}.[[is_automated]]' => true,
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[lobby_id]]' => Lobby3::findOne(['key' => 'xmatch'])->id,
                    '{{%battle3}}.[[use_for_entire]]' => true,
                    '{{exclude1}}.[[player_id]]' => null,
                    // '{{exclude2}}.[[player_id]]' => null,
                ],
                ['not', ['{{%battle3}}.[[rule_id]]' => null]],
                ['not', ['{{clothing}}.[[ability_id]]' => null]],
                ['not', ['{{headgear}}.[[ability_id]]' => null]],
                ['not', ['{{shoes}}.[[ability_id]]' => null]],
                ['or',
                    ['not', ['{{%battle3}}.[[x_power_before]]' => null]],
                    ['not', ['{{%battle3}}.[[x_power_after]]' => null]],
                ],
            ])
            ->orderBy(['{{%battle_player3}}.[[id]]' => SORT_ASC])
            ->limit(800)
            ->with([
                'clothing',
                'clothing.ability',
                'clothing.gearConfigurationSecondary3s.ability',
                'headgear',
                'headgear.ability',
                'headgear.gearConfigurationSecondary3s.ability',
                'shoes',
                'shoes.ability',
                'shoes.gearConfigurationSecondary3s.ability',
            ]);

        $abilityMap = ArrayHelper::map(
            Ability3::find()->asArray()->all(),
            'key',
            'id',
        );

        $minimumId = null;
        do {
            $continue = Yii::$app->db->transaction(
                function (Connection $db) use ($query, &$exclude1, $abilityMap, &$minimumId): bool {
                    $exclude1->where('player_id > :id', [':id' => $minimumId]);

                    $thisQuery = clone $query;
                    if ($minimumId !== null) {
                        $thisQuery->andWhere(['>', '{{%battle_player3}}.[[id]]', $minimumId]);
                    }

                    echo "Query...\n";
                    echo $thisQuery->createCommand()->rawSql . "\n";

                    if (!$players = $thisQuery->all()) {
                        echo "No players!\n";
                        return false;
                    }

                    $inserts = [];
                    foreach ($players as $player) {
                        echo "Player #{$player->id}\n";
                        $minimumId = (int)$player->id;

                        if (!$gpData = Battle3Helper::calcGPs($player)) {
                            continue;
                        }

                        foreach ($gpData as $k => $v) {
                            $inserts[] = [$player->id, $abilityMap[$k], $v];
                        }
                    }

                    $db->createCommand()->batchInsert(
                        '{{%battle_player_gear_power3}}',
                        ['player_id', 'ability_id', 'gear_power'],
                        $inserts,
                    )->execute();

                    return true;
                },
                Transaction::REPEATABLE_READ,
            );
        } while ($continue);

        return 0;
    }
}

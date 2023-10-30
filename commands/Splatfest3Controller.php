<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands;

use LogicException;
use Yii;
use app\models\Lobby3;
use app\models\Rule3;
use app\models\Splatfest3;
use app\models\Splatfest3StatsPower;
use app\models\Splatfest3StatsPowerHistogram;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function array_keys;
use function array_map;
use function gmdate;
use function implode;
use function sprintf;
use function vsprintf;

use const DATE_ATOM;
use const SORT_ASC;

final class Splatfest3Controller extends Controller
{
    public $defaultAction = 'update';

    public function actionUpdate(bool $force = false): int
    {
        return $this->actionUpdateStats($force);
    }

    public function actionUpdateStats(bool $force = false): int
    {
        $status = Yii::$app->db->transaction(
            function (Connection $db) use ($force): int {
                $fests = $this->getUpdateTargetFests($db, $force);
                if (!$fests) {
                    $this->stderr("No target fests\n");
                    return ExitCode::OK;
                }

                return $this->updatePowerStats($db, $fests)
                    ? ExitCode::OK
                    : ExitCode::UNSPECIFIED_ERROR;
            },
            Transaction::REPEATABLE_READ,
        );

        if ($status === ExitCode::OK) {
            if (!$this->vacuumStatsTables(Yii::$app->db)) {
                $status = ExitCode::UNSPECIFIED_ERROR;
            }
        }

        return $status;
    }

    /**
     * @return Splatfest3[]
     */
    private function getUpdateTargetFests(Connection $db, bool $force): array
    {
        return Splatfest3::find()
            ->orderBy(['id' => SORT_ASC])
            ->andWhere(['and',
                $force
                    ? '1 = 1'
                    : ['>=', 'end_at', gmdate(DATE_ATOM, $_SERVER['REQUEST_TIME'] - 2 * 86400)],
            ])
            ->all($db);
    }

    /**
     * @param Splatfest3[] $targetFests
     */
    private function updatePowerStats(Connection $db, array $targetFests): bool
    {
        return $this->updatePowerStatsAbstract($db, $targetFests) &&
            $this->updatePowerStatsHistogram($db, $targetFests);
    }

    /**
     * @param Splatfest3[] $targetFests
     */
    private function updatePowerStatsAbstract(Connection $db, array $targetFests): bool
    {
        if (!$targetFests) {
            return true;
        }

        $this->stderr("Updating splatfest3_stats_power...\n");

        $festIdList = array_map(
            fn (Splatfest3 $fest): int => $fest->id,
            $targetFests,
        );

        $lobby = Lobby3::find()
            ->andWhere(['key' => 'splatfest_challenge'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        $rule = Rule3::find()
            ->andWhere(['key' => 'nawabari'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        if (!$lobby || !$rule) {
            throw new LogicException('Lobby or Rule not found');
        }

        $aggBattles = 'SUM(CASE WHEN {{%battle3}}.[[fest_power]] IS NOT NULL THEN 1 ELSE 0 END)';
        $query = (new Query())
            ->select([
                'splatfest_id' => '{{%splatfest3}}.[[id]]',
                'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                'battles' => 'COUNT(*)',
                'agg_battles' => $aggBattles,
                'average' => 'AVG({{%battle3}}.[[fest_power]])',
                'stddev' => 'STDDEV_SAMP({{%battle3}}.[[fest_power]])',
                'minimum' => 'MIN({{%battle3}}.[[fest_power]])',
                'p05' => 'PERCENTILE_DISC(0.05) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p25' => 'PERCENTILE_DISC(0.25) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p50' => 'PERCENTILE_DISC(0.50) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p75' => 'PERCENTILE_DISC(0.75) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p80' => 'PERCENTILE_DISC(0.80) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'p95' => 'PERCENTILE_DISC(0.95) WITHIN GROUP (ORDER BY {{%battle3}}.[[fest_power]])',
                'maximum' => 'MAX({{%battle3}}.[[fest_power]])',
                'histogram_width' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                    $aggBattles,
                    'STDDEV_SAMP({{%battle3}}.[[fest_power]])',
                ]),
                'last_posted_at' => 'MAX({{%battle3}}.[[created_at]])',
            ])
            ->from('{{%battle3}}')
            ->innerJoin(
                '{{%splatfest3}}',
                implode(' AND ', [
                    "{{%battle3}}.[[start_at]] >= {{%splatfest3}}.[[start_at]] - '1 hour'::interval",
                    "{{%battle3}}.[[start_at]] <= {{%splatfest3}}.[[end_at]] + '1 hour'::interval",
                ]),
            )
            ->andWhere([
                '{{%battle3}}.[[has_disconnect]]' => false,
                '{{%battle3}}.[[is_automated]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                '{{%battle3}}.[[rule_id]]' => $rule->id,
                '{{%battle3}}.[[use_for_entire]]' => true,
                '{{%splatfest3}}.[[id]]' => $festIdList,
            ])
            ->andWhere('{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]')
            ->groupBy(['{{%splatfest3}}.[[id]]']);

        Splatfest3StatsPower::deleteAll(['splatfest_id' => $festIdList]);
        $db
            ->createCommand(
                vsprintf('INSERT INTO %s ( %s ) %s', [
                    $db->quoteTableName(Splatfest3StatsPower::tableName()),
                    implode(
                        ', ',
                        array_map(
                            fn (string $name): string => $db->quoteColumnName($name),
                            array_keys($query->select),
                        ),
                    ),
                    $query->createCommand($db)->rawSql,
                ]),
            )
            ->execute();

        return true;
    }

    /**
     * @param Splatfest3[] $targetFests
     */
    private function updatePowerStatsHistogram(Connection $db, array $targetFests): bool
    {
        if (!$targetFests) {
            return true;
        }

        $this->stderr("Updating splatfest3_stats_power_histogram...\n");

         $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
             '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
             $db->quoteTableName('{{%battle3}}'),
             $db->quoteTableName('{{%splatfest3_stats_power}}'),
             $db->quoteColumnName('fest_power'),
             $db->quoteColumnName('histogram_width'),
         );

        $festIdList = array_map(
            fn (Splatfest3 $fest): int => $fest->id,
            $targetFests,
        );

        $lobby = Lobby3::find()
            ->andWhere(['key' => 'splatfest_challenge'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        $rule = Rule3::find()
            ->andWhere(['key' => 'nawabari'])
            ->limit(1)
            ->cache(86400)
            ->one($db);

        if (!$lobby || !$rule) {
            throw new LogicException('Lobby or Rule not found');
        }

        $query = (new Query())
            ->select([
                'splatfest_id' => '{{%splatfest3}}.[[id]]',
                'class_value' => $classValue,
                'battles' => 'COUNT(*)',
            ])
            ->from('{{%battle3}}')
            ->innerJoin(
                '{{%splatfest3}}',
                implode(' AND ', [
                    "{{%battle3}}.[[start_at]] >= {{%splatfest3}}.[[start_at]] - '1 hour'::interval",
                    "{{%battle3}}.[[start_at]] <= {{%splatfest3}}.[[end_at]] + '1 hour'::interval",
                ]),
            )
            ->innerJoin(
                '{{%splatfest3_stats_power}}',
                '{{%splatfest3}}.[[id]] = {{%splatfest3_stats_power}}.[[splatfest_id]]',
            )
            ->andWhere([
                '{{%battle3}}.[[has_disconnect]]' => false,
                '{{%battle3}}.[[is_automated]]' => true,
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%battle3}}.[[lobby_id]]' => $lobby->id,
                '{{%battle3}}.[[rule_id]]' => $rule->id,
                '{{%battle3}}.[[use_for_entire]]' => true,
                '{{%splatfest3}}.[[id]]' => $festIdList,
            ])
            ->andWhere('{{%battle3}}.[[start_at]] < {{%battle3}}.[[end_at]]')
            ->andWhere(['not', ['{{%battle3}}.[[fest_power]]' => null]])
            ->groupBy([
                '{{%splatfest3}}.[[id]]',
                $classValue,
            ]);

        Splatfest3StatsPowerHistogram::deleteAll(['splatfest_id' => $festIdList]);
        $db
            ->createCommand(
                vsprintf('INSERT INTO %s ( %s ) %s', [
                    $db->quoteTableName(Splatfest3StatsPowerHistogram::tableName()),
                    implode(
                        ', ',
                        array_map(
                            fn (string $name): string => $db->quoteColumnName($name),
                            array_keys($query->select),
                        ),
                    ),
                    $query->createCommand($db)->rawSql,
                ]),
            )
            ->execute();

        return true;
    }

    private function vacuumStatsTables(Connection $db): bool
    {
        $tables = [
            Splatfest3StatsPower::tableName(),
            Splatfest3StatsPowerHistogram::tableName(),
        ];

        foreach ($tables as $table) {
            $this->vacuumTable($db, $table);
        }

        return true;
    }

    private function vacuumTable(Connection $db, string $table): bool
    {
        $this->stderr("Vacuuming {$table}...\n");
        $db->createCommand('VACUUM ( ANALYZE ) ' . $db->quoteTableName($table))->execute();
        return true;
    }
}

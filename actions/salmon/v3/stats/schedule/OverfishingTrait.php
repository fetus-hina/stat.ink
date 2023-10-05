<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\schedule;

use app\components\helpers\TypeHelper;
use app\models\SalmonSchedule3;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;

use function implode;
use function vsprintf;

use const SORT_ASC;

/**
 * @phpstan-type TotalEggs array{total_3_night: int, total_2_night: int, total_1_night: int, total_0_night: int}
 * @phpstan-type OverfishingStats array{total: TotalEggs}
 */
trait OverfishingTrait
{
    /**
     * @phpstan-return OverfishingStats|null
     */
    private function getOverfishingStats(
        Connection $db,
        User $user,
        SalmonSchedule3 $schedule,
    ): ?array {
        return $db->transaction(
            function (Connection $db) use ($user, $schedule): ?array {
                if ($this->createOverfishingEventWaveCountTmpTable($db, $user, $schedule) < 1) {
                    return null;
                }

                return [
                    'total' => $this->getOverfishingStatsTotal($db, $user, $schedule),
                ];
            },
            Transaction::REPEATABLE_READ,
        );
    }

    /**
     * Creates temporary table "{{%tmp_salmon3_event_wave_count}}" for overfishing stats
     *
     * @internal
     * @return int Number of jobs
     */
    private function createOverfishingEventWaveCountTmpTable(
        Connection $db,
        User $user,
        SalmonSchedule3 $schedule,
    ): int {
        $select = (new Query())
            ->select([
                'salmon_id' => '{{%salmon3}}.[[id]]',
                'event_waves' => vsprintf('SUM(CASE %s END)', [
                    implode(' ', [
                        'WHEN {{%salmon_wave3}}.[[event_id]] IS NULL THEN 0',
                        'ELSE 1',
                    ]),
                ]),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_wave3}}',
                '{{%salmon3}}.[[id]] = {{%salmon_wave3}}.[[salmon_id]]',
            )
            ->andWhere([
                '{{%salmon3}}.[[clear_waves]]' => 3,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[is_eggstra_work]]' => false,
                '{{%salmon3}}.[[is_private]]' => false,
                '{{%salmon3}}.[[schedule_id]]' => (int)$schedule->id,
                '{{%salmon3}}.[[user_id]]' => (int)$user->id,
            ])
            ->groupBy(['{{%salmon3}}.[[id]]'])
            ->orderBy(['{{%salmon3}}.[[id]]' => SORT_ASC]);

        $sql = vsprintf('CREATE TEMPORARY TABLE %s ( %s ) ON COMMIT DROP', [
            $db->quoteTableName('{{%tmp_salmon3_event_wave_count}}'),
            implode(', ', [
                '[[salmon_id]] BIGINT NOT NULL PRIMARY KEY',
                '[[event_waves]] INTEGER NOT NULL CHECK ([[event_waves]] BETWEEN 0 AND 3)',
            ]),
        ]);
        $db->createCommand($sql)->execute();

        $sql = vsprintf('INSERT INTO %s %s', [
            $db->quoteTableName('{{%tmp_salmon3_event_wave_count}}'),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return TypeHelper::int(
            $db
                ->createCommand('SELECT COUNT(*) FROM {{%tmp_salmon3_event_wave_count}}')
                ->queryScalar(),
        );
    }

    /**
     * @phpstan-return TotalEggs
     */
    private function getOverfishingStatsTotal(
        Connection $db,
        User $user,
        SalmonSchedule3 $schedule,
    ): array {
        $totalNight = fn (int $n): string => vsprintf(
            'MAX(CASE WHEN %s.%s <= %d THEN %s.%s ELSE 0 END)',
            [
                $db->quoteTableName('{{%tmp_salmon3_event_wave_count}}'),
                $db->quoteColumnName('event_waves'),
                $n,
                $db->quoteTableName('{{%salmon3}}'),
                $db->quoteColumnName('golden_eggs'),
            ],
        );

        $select = (new Query())
            ->select([
                'total_3_night' => $totalNight(3),
                'total_2_night' => $totalNight(2),
                'total_1_night' => $totalNight(1),
                'total_0_night' => $totalNight(0),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%tmp_salmon3_event_wave_count}}',
                '{{%salmon3}}.[[id]] = {{%tmp_salmon3_event_wave_count}}.[[salmon_id]]',
            )
            ->andWhere([
                '{{%salmon3}}.[[clear_waves]]' => 3,
                '{{%salmon3}}.[[is_deleted]]' => false,
                '{{%salmon3}}.[[is_eggstra_work]]' => false,
                '{{%salmon3}}.[[is_private]]' => false,
                '{{%salmon3}}.[[schedule_id]]' => (int)$schedule->id,
                '{{%salmon3}}.[[user_id]]' => (int)$user->id,
            ]);
        return TypeHelper::array($select->createCommand($db)->queryOne());
    }
}

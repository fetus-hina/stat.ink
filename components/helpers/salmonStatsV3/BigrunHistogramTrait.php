<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use Throwable;
use Yii;
use app\components\helpers\CriticalSection;
use app\models\StatBigrunDistribJobAbstract3;
use app\models\StatBigrunDistribJobHistogram3;
use app\models\StatBigrunDistribUserAbstract3;
use app\models\StatBigrunDistribUserHistogram3;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function implode;
use function sprintf;
use function vsprintf;

trait BigrunHistogramTrait
{
    protected static function createBigrunHistogramStats(
        Connection $db,
        bool $updateJobStats = false,
    ): bool {
        $lock = null;
        try {
            try {
                $lock = CriticalSection::lock(__METHOD__, 60, Yii::$app->pgMutex);
            } catch (Throwable $e) {
                return true;
            }

            self::bigrunHistogramUserAbstract($db);
            self::bigrunHistogramUserDistrib($db);
            if ($updateJobStats) {
                self::bigrunHistogramJobAbstract($db);
                self::bigrunHistogramJobDistrib($db);
            }

            return true;
        } catch (Throwable $e) {
            Yii::error(
                vsprintf('Catch %s, message=%s', [
                    $e::class,
                    $e->getMessage(),
                ]),
                __METHOD__,
            );
            $db->transaction->rollBack();
            return false;
        } finally {
            if ($lock) {
                unset($lock);
            }
        }
    }

    private static function bigrunHistogramUserAbstract(Connection $db): void
    {
        StatBigrunDistribUserAbstract3::deleteAll();

        $p = fn (float $pct): string => sprintf(
            'PERCENTILE_DISC(%.02f) WITHIN GROUP (ORDER BY %s)',
            $pct,
            '{{%user_stat_bigrun3}}.[[golden_eggs]]',
        );

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%user_stat_bigrun3}}.[[schedule_id]]',
                'users' => 'COUNT(*)',
                'average' => 'AVG({{%user_stat_bigrun3}}.[[golden_eggs]])',
                'stddev' => 'STDDEV_SAMP({{%user_stat_bigrun3}}.[[golden_eggs]])',
                'min' => 'MIN({{%user_stat_bigrun3}}.[[golden_eggs]])',
                'p05' => $p(0.05),
                'p25' => $p(0.25),
                'p50' => $p(0.50),
                'p75' => $p(0.75),
                'p80' => $p(0.80),
                'p95' => $p(0.95),
                'max' => 'MAX({{%user_stat_bigrun3}}.[[golden_eggs]])',
                'histogram_width' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                    'COUNT(*)',
                    'STDDEV_SAMP({{%user_stat_bigrun3}}.[[golden_eggs]])',
                ]),
            ])
            ->from('{{%user_stat_bigrun3}}')
            ->groupBy([
                '{{%user_stat_bigrun3}}.[[schedule_id]]',
            ]);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            '{{%stat_bigrun_distrib_user_abstract3}}',
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();
    }

    private static function bigrunHistogramUserDistrib(Connection $db): void
    {
        StatBigrunDistribUserHistogram3::deleteAll();

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%user_stat_bigrun3}}'),
            $db->quoteTableName('{{%stat_bigrun_distrib_user_abstract3}}'),
            $db->quoteColumnName('golden_eggs'),
            $db->quoteColumnName('histogram_width'),
        );

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%user_stat_bigrun3}}.[[schedule_id]]',
                'class_value' => $classValue,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%user_stat_bigrun3}}')
            ->innerJoin(
                '{{%stat_bigrun_distrib_user_abstract3}}',
                '{{%user_stat_bigrun3}}.[[schedule_id]] = {{%stat_bigrun_distrib_user_abstract3}}.[[schedule_id]]',
            )
            ->andWhere(['>', '{{%stat_bigrun_distrib_user_abstract3}}.[[histogram_width]]', 0])
            ->groupBy([
                '{{%user_stat_bigrun3}}.[[schedule_id]]',
                $classValue,
            ]);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            '{{%stat_bigrun_distrib_user_histogram3}}',
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();
    }

    protected static function bigrunHistogramJobAbstract(Connection $db): void
    {
        StatBigrunDistribJobAbstract3::deleteAll();

        $p = fn (float $pct): string => sprintf(
            'PERCENTILE_DISC(%.02f) WITHIN GROUP (ORDER BY %s)',
            $pct,
            '{{%salmon3}}.[[golden_eggs]]',
        );

        $columnJobs = fn (int $clearWaves): string => vsprintf('SUM(CASE %s END)', [
            implode(' ', [
                "WHEN {{%salmon3}}.[[clear_waves]] = {$clearWaves} THEN 1",
                'ELSE 0',
            ]),
        ]);
        $columnAvg = fn (int $clearWaves): string => vsprintf('AVG(CASE %s END)', [
            implode(' ', [
                "WHEN {{%salmon3}}.[[clear_waves]] = {$clearWaves} THEN {{%salmon3}}.[[golden_eggs]]",
                'ELSE NULL',
            ]),
        ]);
        $columnSD = fn (int $clearWaves): string => vsprintf('STDDEV_SAMP(CASE %s END)', [
            implode(' ', [
                "WHEN {{%salmon3}}.[[clear_waves]] = {$clearWaves} THEN {{%salmon3}}.[[golden_eggs]]",
                'ELSE NULL',
            ]),
        ]);

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%salmon3}}.[[schedule_id]]',
                'users' => 'COUNT(DISTINCT {{%salmon3}}.[[user_id]])',
                'jobs' => 'COUNT(*)',
                'average' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                'stddev' => 'STDDEV_SAMP({{%salmon3}}.[[golden_eggs]])',
                'clear_jobs' => $columnJobs(3),
                'clear_average' => $columnAvg(3),
                'clear_stddev' => $columnSD(3),
                'w1_failed_jobs' => $columnJobs(0),
                'w1_failed_average' => $columnAvg(0),
                'w1_failed_stddev' => $columnSD(0),
                'w2_failed_jobs' => $columnJobs(1),
                'w2_failed_average' => $columnAvg(1),
                'w2_failed_stddev' => $columnSD(1),
                'w3_failed_jobs' => $columnJobs(2),
                'w3_failed_average' => $columnAvg(2),
                'w3_failed_stddev' => $columnSD(2),
                'min' => 'MIN({{%salmon3}}.[[golden_eggs]])',
                'p05' => $p(0.05),
                'p25' => $p(0.25),
                'p50' => $p(0.50),
                'p75' => $p(0.75),
                'p80' => $p(0.80),
                'p95' => $p(0.95),
                'max' => 'MAX({{%salmon3}}.[[golden_eggs]])',
                'histogram_width' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                    'COUNT(*)',
                    'STDDEV_SAMP({{%salmon3}}.[[golden_eggs]])',
                ]),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_schedule3}}',
                vsprintf('((%s))', [
                    implode(') AND (', [
                        '{{%salmon3}}.[[schedule_id]] = {{%salmon_schedule3}}.[[id]]',
                        vsprintf('((%s) OR (%s))', [
                            '{{%salmon_schedule3}}.[[big_map_id]] IS NOT NULL',
                            '{{%salmon_schedule3}}.[[is_random_map_big_run]] = FALSE',
                        ]),
                        '{{%salmon_schedule3}}.[[is_eggstra_work]] = FALSE',
                        '{{%salmon_schedule3}}.[[map_id]] IS NULL',
                    ]),
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[has_broken_data]]' => false,
                    '{{%salmon3}}.[[has_disconnect]]' => false,
                    '{{%salmon3}}.[[is_automated]]' => true,
                    '{{%salmon3}}.[[is_big_run]]' => true,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                ['between', '{{%salmon3}}.[[clear_waves]]', 0, 3],
            ])
            ->groupBy([
                '{{%salmon3}}.[[schedule_id]]',
            ]);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            '{{%stat_bigrun_distrib_job_abstract3}}',
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();
    }

    protected static function bigrunHistogramJobDistrib(Connection $db): void
    {
        StatBigrunDistribJobHistogram3::deleteAll();

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%salmon3}}'),
            $db->quoteTableName('{{%stat_bigrun_distrib_job_abstract3}}'),
            $db->quoteColumnName('golden_eggs'),
            $db->quoteColumnName('histogram_width'),
        );

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%salmon3}}.[[schedule_id]]',
                'class_value' => $classValue,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon_schedule3}}',
                vsprintf('((%s))', [
                    implode(') AND (', [
                        '{{%salmon3}}.[[schedule_id]] = {{%salmon_schedule3}}.[[id]]',
                        '{{%salmon_schedule3}}.[[big_map_id]] IS NOT NULL',
                        '{{%salmon_schedule3}}.[[is_eggstra_work]] = FALSE',
                        '{{%salmon_schedule3}}.[[map_id]] IS NULL',
                    ]),
                ]),
            )
            ->innerJoin(
                '{{%stat_bigrun_distrib_job_abstract3}}',
                '{{%salmon3}}.[[schedule_id]] = {{%stat_bigrun_distrib_job_abstract3}}.[[schedule_id]]',
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[has_broken_data]]' => false,
                    '{{%salmon3}}.[[has_disconnect]]' => false,
                    '{{%salmon3}}.[[is_automated]]' => true,
                    '{{%salmon3}}.[[is_big_run]]' => true,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_eggstra_work]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                ],
                ['not', ['{{%salmon3}}.[[golden_eggs]]' => null]],
                ['between', '{{%salmon3}}.[[clear_waves]]', 0, 3],
                ['>', '{{%stat_bigrun_distrib_job_abstract3}}.[[histogram_width]]', 0],
            ])
            ->groupBy([
                '{{%salmon3}}.[[schedule_id]]',
                $classValue,
            ]);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            '{{%stat_bigrun_distrib_job_histogram3}}',
            implode(
                ', ',
                array_map(
                    $db->quoteColumnName(...),
                    array_keys($select->select),
                ),
            ),
            $select->createCommand($db)->rawSql,
        ]);
        $db->createCommand($sql)->execute();
    }
}

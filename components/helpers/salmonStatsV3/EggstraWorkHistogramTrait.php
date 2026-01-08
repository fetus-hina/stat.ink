<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use Throwable;
use Yii;
use app\models\StatEggstraWorkDistribUserAbstract3;
use app\models\StatEggstraWorkDistribUserHistogram3;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function implode;
use function sprintf;
use function vsprintf;

trait EggstraWorkHistogramTrait
{
    protected static function createEggstraWorkHistogramStats(Connection $db): bool
    {
        try {
            return self::eggstraWorkHistogramAbstract($db) &&
                self::eggstraWorkHistogramDistrib($db);
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
        }
    }

    private static function eggstraWorkHistogramDistrib(Connection $db): bool
    {
        StatEggstraWorkDistribUserHistogram3::deleteAll();

        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s.%3$s / %2$s.%4$s) + 0.5) * %2$s.%4$s)::integer',
            $db->quoteTableName('{{%user_stat_eggstra_work3}}'),
            $db->quoteTableName('{{%stat_eggstra_work_distrib_user_abstract3}}'),
            $db->quoteColumnName('golden_eggs'),
            $db->quoteColumnName('histogram_width'),
        );

        $select = (new Query())
            ->select([
                'schedule_id' => '{{%user_stat_eggstra_work3}}.[[schedule_id]]',
                'class_value' => $classValue,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%user_stat_eggstra_work3}}')
            ->innerJoin(
                '{{%stat_eggstra_work_distrib_user_abstract3}}',
                '{{%user_stat_eggstra_work3}}.[[schedule_id]] = {{%stat_eggstra_work_distrib_user_abstract3}}.[[schedule_id]]',
            )
            ->andWhere(['>', '{{%stat_eggstra_work_distrib_user_abstract3}}.[[histogram_width]]', 0])
            ->groupBy([
                '{{%user_stat_eggstra_work3}}.[[schedule_id]]',
                $classValue,
            ]);

        $sql = vsprintf('INSERT INTO %s (%s) %s', [
            $db->quoteTableName(StatEggstraWorkDistribUserHistogram3::tableName()),
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

        return true;
    }

    private static function eggstraWorkHistogramAbstract(Connection $db): bool
    {
        StatEggstraWorkDistribUserAbstract3::deleteAll();

        $percentile = fn (float $pos): string => sprintf(
            'PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY [[golden_eggs]] ASC)',
            $pos,
        );

        $select = (new Query())
            ->select([
                'schedule_id',
                'users' => 'COUNT(*)',
                'average' => 'AVG([[golden_eggs]])',
                'stddev' => 'STDDEV_SAMP([[golden_eggs]])',
                'min' => 'MIN([[golden_eggs]])',
                'p05' => $percentile(0.05),
                'p25' => $percentile(0.25),
                'p50' => $percentile(0.50),
                'p75' => $percentile(0.75),
                'p80' => $percentile(0.80),
                'p95' => $percentile(0.95),
                'max' => 'MAX([[golden_eggs]])',
                'histogram_width' => 'HISTOGRAM_WIDTH(COUNT(*), STDDEV_SAMP([[golden_eggs]]), 2)',
            ])
            ->from('{{%user_stat_eggstra_work3}}')
            ->groupBy('schedule_id');

        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName(StatEggstraWorkDistribUserAbstract3::tableName()),
                implode(
                    ', ',
                    array_map($db->quoteColumnName(...), array_keys($select->select)),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        )->execute();

        return true;
    }
}

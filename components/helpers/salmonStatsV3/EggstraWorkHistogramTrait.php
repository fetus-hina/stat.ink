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
use app\models\StatEggstraWorkDistrib3;
use app\models\StatEggstraWorkDistribAbstract3;
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
            return self::eggstraWorkHistogramDistrib($db) &&
                self::eggstraWorkHistogramAbstract($db);
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
        StatEggstraWorkDistrib3::deleteAll('1 = 1');

        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_eggstra_work_distrib3}}'),
                implode(', ', [
                    $db->quoteColumnName('schedule_id'),
                    $db->quoteColumnName('golden_egg'),
                    $db->quoteColumnName('users'),
                ]),
                (new Query())
                    ->select([
                        'schedule_id',
                        'golden_egg' => 'TRUNC([[golden_eggs]] / 5) * 5',
                        'users' => 'COUNT(*)',
                    ])
                    ->from('{{%user_stat_eggstra_work3}}')
                    ->groupBy([
                        'schedule_id',
                        'TRUNC([[golden_eggs]] / 5) * 5',
                    ])
                    ->createCommand($db)
                    ->rawSql,
            ]),
        )->execute();

        return true;
    }

    private static function eggstraWorkHistogramAbstract(Connection $db): bool
    {
        StatEggstraWorkDistribAbstract3::deleteAll('1 = 1');

        $percentile = fn (float $pos): string => sprintf(
            'PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY [[golden_eggs]] DESC)',
            $pos,
        );

        $select = (new Query())
            ->select([
                'schedule_id',
                'users' => 'COUNT(*)',
                'average' => 'AVG([[golden_eggs]])',
                'stddev' => 'STDDEV_SAMP([[golden_eggs]])',
                'min' => 'MIN([[golden_eggs]])',
                'q1' => $percentile(1 - 0.25),
                'median' => $percentile(1 - 0.5),
                'q3' => $percentile(1 - 0.75),
                'max' => 'MAX([[golden_eggs]])',
                'top_5_pct' => $percentile(0.05),
                'top_20_pct' => $percentile(0.20),
            ])
            ->from('{{%user_stat_eggstra_work3}}')
            ->groupBy('schedule_id');

        $db->createCommand(
            vsprintf('INSERT INTO %s ( %s ) %s', [
                $db->quoteTableName('{{%stat_eggstra_work_distrib_abstract3}}'),
                implode(
                    ', ',
                    array_map(
                        fn (string $columnName): string => $db->quoteColumnName($columnName),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]),
        )->execute();

        return true;
    }
}

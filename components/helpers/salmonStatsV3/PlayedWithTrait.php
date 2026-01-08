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
use app\models\Salmon3UserStatsPlayedWith;
use app\models\User;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function array_merge;
use function implode;
use function vsprintf;

trait PlayedWithTrait
{
    protected static function createPlayedWithStats(Connection $db, User $user): bool
    {
        try {
            $select = (new Query())
                ->select(
                    array_merge(
                        [
                            'user_id' => '{{%salmon3}}.[[user_id]]',
                            'name' => '{{%salmon_player3}}.[[name]]',
                            'number' => '{{%salmon_player3}}.[[number]]',
                            'jobs' => 'COUNT(*)',
                            'clear_jobs' => vsprintf('SUM(CASE %s END)', [
                                implode(' ', [
                                    'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN 1',
                                    'ELSE 0',
                                ]),
                            ]),
                            'clear_waves' => vsprintf('SUM(CASE %s END)', [
                                implode(' ', [
                                    'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN 3',
                                    'ELSE {{%salmon3}}.[[clear_waves]]',
                                ]),
                            ]),
                            'max_danger_rate_cleared' => vsprintf('MAX(CASE %s END)', [
                                implode(' ', [
                                    'WHEN {{%salmon3}}.[[clear_waves]] >= 3 THEN {{%salmon3}}.[[danger_rate]]',
                                    'ELSE NULL',
                                ]),
                            ]),
                            'max_danger_rate_played' => 'MAX({{%salmon3}}.[[danger_rate]])',
                        ],
                        self::makePlayedWithStatsRecords('team_golden_egg', '{{%salmon3}}.[[golden_eggs]]'),
                        self::makePlayedWithStatsRecords('golden_egg', '{{%salmon_player3}}.[[golden_eggs]]'),
                        self::makePlayedWithStatsRecords('rescue', '{{%salmon_player3}}.[[rescue]]'),
                        self::makePlayedWithStatsRecords('rescued', '{{%salmon_player3}}.[[rescued]]'),
                        self::makePlayedWithStatsRecords('defeat_boss', '{{%salmon_player3}}.[[defeat_boss]]'),
                    ),
                )
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_player3}}',
                    implode(' AND ', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = FALSE',
                    ]),
                )
                ->andWhere(['and',
                    [
                        '{{%salmon3}}.[[is_deleted]]' => false,
                        '{{%salmon3}}.[[is_eggstra_work]]' => false,
                        '{{%salmon3}}.[[is_private]]' => false,
                        '{{%salmon3}}.[[user_id]]' => $user->id,
                    ],
                    ['not', ['{{%salmon3}}.[[clear_waves]]' => null]],
                    ['not', ['{{%salmon_player3}}.[[name]]' => null]],
                    ['not', ['{{%salmon_player3}}.[[number]]' => null]],
                ])
                ->groupBy([
                    '{{%salmon3}}.[[user_id]]',
                    '{{%salmon_player3}}.[[name]]',
                    '{{%salmon_player3}}.[[number]]',
                ]);

            Salmon3UserStatsPlayedWith::deleteAll(['user_id' => $user->id]);
            $insertSql = vsprintf('INSERT INTO %s (%s) %s', [
                $db->quoteTableName(Salmon3UserStatsPlayedWith::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($select->select),
                    ),
                ),
                $select->createCommand($db)->rawSql,
            ]);
            $db->createCommand($insertSql)->execute();

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
        }
    }

    private static function makePlayedWithStatsRecords(string $prefix, string $column): array
    {
        $pct = fn (float $p): string => vsprintf('PERCENTILE_DISC(%.2f) WITHIN GROUP (ORDER BY %s)', [
            $p,
            $column,
        ]);

        return [
            "{$prefix}_avg" => "AVG($column)",
            "{$prefix}_sd" => "STDDEV_POP($column)",
            "{$prefix}_max" => "MAX($column)",
            "{$prefix}_95" => $pct(0.95),
            "{$prefix}_75" => $pct(0.75),
            "{$prefix}_50" => $pct(0.50),
            "{$prefix}_25" => $pct(0.25),
            "{$prefix}_05" => $pct(0.05),
            "{$prefix}_min" => "MIN($column)",
        ];
    }
}

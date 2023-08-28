<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use DateTimeImmutable;
use Throwable;
use Yii;
use app\models\Salmon3UserStatsGoldenEgg;
use app\models\Salmon3UserStatsGoldenEggIndividualHistogram;
use app\models\Salmon3UserStatsGoldenEggTeamHistogram;
use app\models\User;
use yii\db\ActiveRecord;
use yii\db\Connection;
use yii\db\Query;

use function array_keys;
use function array_map;
use function implode;
use function sprintf;
use function vsprintf;

trait UserGoldenEggTrait
{
    use StatsTrait;

    protected static function createUserGoldenEggStats(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        try {
            self::updateUserGoldenEggStatsAbstract($db, $user, $now);
            self::updateUserGoldenEggStatsTeamHistogram($db, $user, $now);
            self::updateUserGoldenEggStatsIndividualHistogram($db, $user, $now);

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

    private static function updateUserGoldenEggStatsAbstract(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): void {
        $query = (new Query())
            ->select([
                'user_id' => '{{%salmon3}}.[[user_id]]',
                'map_id' => '{{%salmon3}}.[[stage_id]]',
                'shifts' => 'COUNT(*)',
                'average_team' => 'AVG({{%salmon3}}.[[golden_eggs]])',
                'stddev_team' => 'STDDEV_POP({{%salmon3}}.[[golden_eggs]])',
                'histogram_width_team' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                    'COUNT(*)',
                    'STDDEV_POP({{%salmon3}}.[[golden_eggs]])',
                ]),
                'average_individual' => 'AVG({{%salmon_player3}}.[[golden_eggs]])',
                'stddev_individual' => 'STDDEV_POP({{%salmon_player3}}.[[golden_eggs]])',
                'histogram_width_individual' => vsprintf('HISTOGRAM_WIDTH(%s, %s)', [
                    'SUM(CASE WHEN {{%salmon_player3}}.[[id]] IS NOT NULL THEN 1 ELSE 0 END)',
                    'STDDEV_POP({{%salmon_player3}}.[[golden_eggs]])',
                ]),
            ])
            ->from('{{%salmon3}}')
            ->leftJoin(
                '{{%salmon_player3}}',
                vsprintf('((%s))', [
                    implode(') AND (', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = TRUE',
                        '{{%salmon_player3}}.[[golden_eggs]] IS NOT NULL',
                    ]),
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[big_stage_id]]' => null,
                    '{{%salmon3}}.[[is_big_run]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                ],
                ['not', ['{{%salmon3}}.[[stage_id]]' => null]],
                ['<=', '{{%salmon3}}.[[created_at]]', $now->format(DateTimeImmutable::ATOM)],
            ])
            ->andWhere(self::getBasicStatsCond())
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon3}}.[[stage_id]]',
            ]);

        $insert = vsprintf(
            'INSERT INTO %s ( %s ) %s',
            [
                $db->quoteTableName(Salmon3UserStatsGoldenEgg::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($query->select),
                    ),
                ),
                $query->createCommand($db)->rawSql,
            ],
        );

        Salmon3UserStatsGoldenEgg::deleteAll(['user_id' => $user->id]);
        $db->createCommand($insert)->execute();
    }

    private static function updateUserGoldenEggStatsTeamHistogram(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): void {
        self::updateUserGoldenEggStatsHistogram(
            $db,
            $user,
            $now,
            Salmon3UserStatsGoldenEggTeamHistogram::class,
            '{{%salmon3}}.[[golden_eggs]]',
            '{{%salmon3_user_stats_golden_egg}}.[[histogram_width_team]]',
        );
    }

    private static function updateUserGoldenEggStatsIndividualHistogram(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): void {
        self::updateUserGoldenEggStatsHistogram(
            $db,
            $user,
            $now,
            Salmon3UserStatsGoldenEggIndividualHistogram::class,
            '{{%salmon_player3}}.[[golden_eggs]]',
            '{{%salmon3_user_stats_golden_egg}}.[[histogram_width_individual]]',
        );
    }

    /**
     * @param class-string<ActiveRecord> $modelClass
     */
    private static function updateUserGoldenEggStatsHistogram(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
        string $modelClass,
        string $targetColumn,
        string $widthColumn,
    ): void {
        $classValue = sprintf(
            // +0.5 は階級値は階級の幅の中央を表すための調整
            '((FLOOR(%1$s::numeric / %2$s::numeric) + 0.5) * %2$s::numeric)::integer',
            $targetColumn,
            $widthColumn,
        );

        $query = (new Query())
            ->select([
                'user_id' => '{{%salmon3}}.[[user_id]]',
                'map_id' => '{{%salmon3}}.[[stage_id]]',
                'class_value' => $classValue,
                'count' => 'COUNT(*)',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin(
                '{{%salmon3_user_stats_golden_egg}}',
                vsprintf('((%s))', [
                    implode(') AND (', [
                        '{{%salmon3}}.[[user_id]] = {{%salmon3_user_stats_golden_egg}}.[[user_id]]',
                        '{{%salmon3}}.[[stage_id]] = {{%salmon3_user_stats_golden_egg}}.[[map_id]]',
                    ]),
                ]),
            )
            ->leftJoin(
                '{{%salmon_player3}}',
                vsprintf('((%s))', [
                    implode(') AND (', [
                        '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                        '{{%salmon_player3}}.[[is_me]] = TRUE',
                        '{{%salmon_player3}}.[[golden_eggs]] IS NOT NULL',
                    ]),
                ]),
            )
            ->andWhere(['and',
                [
                    '{{%salmon3}}.[[big_stage_id]]' => null,
                    '{{%salmon3}}.[[is_big_run]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                ],
                ['not', ['{{%salmon3}}.[[stage_id]]' => null]],
                ['<=', '{{%salmon3}}.[[created_at]]', $now->format(DateTimeImmutable::ATOM)],
            ])
            ->andWhere(self::getBasicStatsCond())
            ->groupBy([
                '{{%salmon3}}.[[user_id]]',
                '{{%salmon3}}.[[stage_id]]',
                $classValue,
            ]);

        $insert = vsprintf(
            'INSERT INTO %s ( %s ) %s',
            [
                $db->quoteTableName($modelClass::tableName()),
                implode(
                    ', ',
                    array_map(
                        $db->quoteColumnName(...),
                        array_keys($query->select),
                    ),
                ),
                $query->createCommand($db)->rawSql,
            ],
        );

        $modelClass::deleteAll(['user_id' => $user->id]);
        $db->createCommand($insert)->execute();
    }
}

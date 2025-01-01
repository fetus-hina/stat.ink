<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use DateTimeImmutable;
use Exception;
use Throwable;
use Yii;
use app\models\User;
use app\models\UserStatSalmon3;
use yii\db\Connection;
use yii\db\Query;

use function array_merge;
use function implode;
use function vsprintf;

use const SORT_DESC;

trait StatsTrait
{
    protected static function createUserStats(Connection $db, User $user, DateTimeImmutable $now): bool
    {
        try {
            $model = UserStatSalmon3::find()->andWhere(['user_id' => $user->id])->limit(1)->one()
                ?? Yii::createObject([
                    'class' => UserStatSalmon3::class,
                    'user_id' => $user->id,
                ]);

            $model->attributes = array_merge(
                self::makeBasicStats($db, $user, $now),
                self::makeTitleStats($db, $user, $now),
            );

            if (!$model->save()) {
                throw new Exception('Failed to save');
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
        }
    }

    private static function makeBasicStats(Connection $db, User $user, DateTimeImmutable $now): array
    {
        $condBasic = self::getBasicStatsCond();
        $condKing = '{{%salmon3}}.[[king_salmonid_id]] IS NOT NULL';

        $aggregate = fn (?string $additionalCond = null, string $then = '1', string $else = '0') => vsprintf(
            'SUM(CASE WHEN %s AND %s THEN %s ELSE %s END)',
            [
                $condBasic,
                $additionalCond ?? 'TRUE',
                $then,
                $else,
            ],
        );

        return (new Query())
            ->select([
                'jobs' => 'COUNT(*)',
                'agg_jobs' => $aggregate(),
                'clear_jobs' => $aggregate('{{%salmon3}}.[[clear_waves]] >= 3'),
                'total_waves' => $aggregate(null, 'LEAST(3, {{%salmon3}}.[[clear_waves]] + 1)'),
                'clear_waves' => $aggregate(null, '{{%salmon3}}.[[clear_waves]]'),
                'king_appearances' => $aggregate($condKing),
                'king_defeated' => $aggregate("{$condKing} AND {{%salmon3}}.[[clear_extra]] = TRUE"),
                'golden_eggs' => $aggregate(null, '{{%salmon_player3}}.[[golden_eggs]]'),
                'power_eggs' => $aggregate(null, '{{%salmon_player3}}.[[power_eggs]]'),
                'rescues' => $aggregate(null, '{{%salmon_player3}}.[[rescue]]'),
                'rescued' => $aggregate(null, '{{%salmon_player3}}.[[rescued]]'),
                'peak_danger_rate' => vsprintf('MAX(CASE %s END)', [
                    "WHEN {$condBasic} AND {{%salmon3}}.[[clear_waves]] >= 3 THEN {{%salmon3}}.[[danger_rate]]",
                    'ELSE NULL',
                ]),
                'boss_defeated' => $aggregate(null, '{{%salmon_player3}}.[[defeat_boss]]'),
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_player3}}', implode(' AND ', [
                '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                '{{%salmon_player3}}.[[is_me]] = TRUE',
            ]))
            ->andWhere([
                '{{%salmon3}}.[[user_id]]' => $user->id,
                '{{%salmon3}}.[[is_deleted]]' => false,
            ])
            ->one($db);
    }

    private static function makeTitleStats(Connection $db, User $user, DateTimeImmutable $now): array
    {
        return (new Query())
            ->select([
                'peak_title_id' => '{{%salmon3}}.[[title_after_id]]',
                'peak_title_exp' => 'MAX({{%salmon3}}.[[title_exp_after]])',
            ])
            ->from('{{%salmon3}}')
            ->innerJoin('{{%salmon_player3}}', implode(' AND ', [
                '{{%salmon3}}.[[id]] = {{%salmon_player3}}.[[salmon_id]]',
                '{{%salmon_player3}}.[[is_me]] = TRUE',
            ]))
            ->innerJoin(
                '{{%salmon_title3}}',
                '{{%salmon3}}.[[title_after_id]] = {{%salmon_title3}}.[[id]]',
            )
            ->andWhere([
                '{{%salmon3}}.[[user_id]]' => $user->id,
                '{{%salmon3}}.[[is_deleted]]' => false,
            ])
            ->groupBy([
                '{{%salmon3}}.[[title_after_id]]',
            ])
            ->orderBy([
                'MAX({{%salmon_title3}}.[[rank]])' => SORT_DESC,
            ])
            ->limit(1)
            ->one($db);
    }

    private static function getBasicStatsCond(): string
    {
        // これらすべての条件がそろっているものを最低条件とみなす
        return implode(' AND ', [
            '{{%salmon3}}.[[clear_waves]] IS NOT NULL',
            '{{%salmon3}}.[[danger_rate]] > 0.0',
            '{{%salmon3}}.[[danger_rate]] IS NOT NULL',
            '{{%salmon3}}.[[has_broken_data]] = FALSE',
            '{{%salmon3}}.[[is_eggstra_work]] = FALSE',
            '{{%salmon3}}.[[is_private]] = FALSE',
            '{{%salmon3}}.[[title_after_id]] IS NOT NULL',
            '{{%salmon3}}.[[title_exp_after]] IS NOT NULL',
            '{{%salmon_player3}}.[[defeat_boss]] IS NOT NULL',
            '{{%salmon_player3}}.[[golden_eggs]] IS NOT NULL',
            '{{%salmon_player3}}.[[is_disconnected]] = FALSE',
            '{{%salmon_player3}}.[[power_eggs]] IS NOT NULL',
            '{{%salmon_player3}}.[[rescue]] IS NOT NULL',
            '{{%salmon_player3}}.[[rescued]] IS NOT NULL',
            vsprintf('((%1$s IS NULL) OR (%1$s IS NOT NULL AND %2$s IS NOT NULL))', [
                '{{%salmon3}}.[[king_salmonid_id]]',
                '{{%salmon3}}.[[clear_extra]]',
            ]),
        ]);
    }
}

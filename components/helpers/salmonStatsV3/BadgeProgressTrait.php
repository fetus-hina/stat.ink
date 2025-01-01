<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\salmonStatsV3;

use DateTimeImmutable;
use Yii;
use app\models\User;
use app\models\UserBadge3BossSalmonid;
use app\models\UserBadge3EggsecutiveReached;
use app\models\UserBadge3KingSalmonid;
use yii\db\Connection;
use yii\db\Exception as DbException;
use yii\db\Query;

use function vsprintf;

trait BadgeProgressTrait
{
    private static function createBadgeProgress(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        try {
            return self::updateBadgeProgressKing($db, $user, $now) &&
                self::updateBadgeProgressBoss($db, $user, $now) &&
                self::updateBadgeProgressReached($db, $user, $now);
        } catch (DbException $e) {
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

    private static function updateBadgeProgressKing(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        UserBadge3KingSalmonid::deleteAll(['user_id' => $user->id]);
        $sql = vsprintf('INSERT INTO %s ([[user_id]], [[king_id]], [[count]]) %s', [
            $db->quoteTableName(UserBadge3KingSalmonid::tableName()),
            (new Query())
                ->select([
                    'user_id' => '{{%salmon3}}.[[user_id]]',
                    'king_id' => '{{%salmon3}}.[[king_salmonid_id]]',
                    'count' => 'COUNT(*)',
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_king3}}',
                    '{{%salmon3}}.[[king_salmonid_id]] = {{%salmon_king3}}.[[id]]',
                )
                ->andWhere([
                    '{{%salmon3}}.[[clear_extra]]' => true,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                ])
                ->groupBy([
                    '{{%salmon3}}.[[user_id]]',
                    '{{%salmon3}}.[[king_salmonid_id]]',
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return true;
    }

    private static function updateBadgeProgressBoss(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        UserBadge3BossSalmonid::deleteAll(['user_id' => $user->id]);
        $sql = vsprintf('INSERT INTO %s ([[user_id]], [[boss_id]], [[count]]) %s', [
            $db->quoteTableName(UserBadge3BossSalmonid::tableName()),
            (new Query())
                ->select([
                    'user_id' => '{{%salmon3}}.[[user_id]]',
                    'boss_id' => '{{%salmon_boss_appearance3}}.[[boss_id]]',
                    'count' => 'SUM({{%salmon_boss_appearance3}}.[[defeated_by_me]])',
                ])
                ->from('{{%salmon3}}')
                ->innerJoin(
                    '{{%salmon_boss_appearance3}}',
                    '{{%salmon3}}.[[id]] = {{%salmon_boss_appearance3}}.[[salmon_id]]',
                )
                ->innerJoin(
                    '{{%salmon_boss3}}',
                    '{{%salmon_boss_appearance3}}.[[boss_id]] = {{%salmon_boss3}}.[[id]]',
                )
                ->andWhere([
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                    '{{%salmon_boss3}}.[[has_badge]]' => true,
                ])
                ->groupBy([
                    '{{%salmon3}}.[[user_id]]',
                    '{{%salmon_boss_appearance3}}.[[boss_id]]',
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return true;
    }

    private static function updateBadgeProgressReached(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        UserBadge3EggsecutiveReached::deleteAll(['user_id' => $user->id]);
        $sql = vsprintf('INSERT INTO %s ([[user_id]], [[stage_id]], [[reached]]) %s', [
            $db->quoteTableName(UserBadge3EggsecutiveReached::tableName()),
            (new Query())
                ->select([
                    'user_id' => '{{%salmon3}}.[[user_id]]',
                    'stage_id' => '{{%salmon3}}.[[stage_id]]',
                    'reached' => 'MAX({{%salmon3}}.[[title_exp_after]])',
                ])
                ->from('{{%salmon3}}')
                ->innerJoin('{{%salmon_title3}}', '{{%salmon3}}.[[title_after_id]] = {{%salmon_title3}}.[[id]]')
                ->innerJoin('{{%salmon_map3}}', '{{%salmon3}}.[[stage_id]] = {{%salmon_map3}}.[[id]]')
                ->andWhere([
                    '{{%salmon3}}.[[is_big_run]]' => false,
                    '{{%salmon3}}.[[is_deleted]]' => false,
                    '{{%salmon3}}.[[is_private]]' => false,
                    '{{%salmon3}}.[[user_id]]' => $user->id,
                    '{{%salmon_title3}}.[[key]]' => 'eggsecutive_vp',
                ])
                ->groupBy([
                    '{{%salmon3}}.[[user_id]]',
                    '{{%salmon3}}.[[stage_id]]',
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return true;
    }
}

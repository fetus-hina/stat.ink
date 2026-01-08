<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\userStatsV3;

use DateTimeImmutable;
use Yii;
use app\models\User;
use app\models\UserBadge3Rule;
use app\models\UserBadge3Special;
use app\models\UserBadge3Tricolor;
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
            return self::updateBadgeProgressRules($db, $user, $now) &&
                self::updateBadgeProgressSpecials($db, $user, $now) &&
                self::updateBadgeProgressTricolor($db, $user, $now);
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

    private static function updateBadgeProgressRules(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        UserBadge3Rule::deleteAll(['user_id' => $user->id]);
        $sql = vsprintf('INSERT INTO %s ([[user_id]], [[rule_id]], [[count]]) %s', [
            $db->quoteTableName(UserBadge3Rule::tableName()),
            (new Query())
                ->select([
                    'user_id' => '{{%battle3}}.[[user_id]]',
                    'rule_id' => '{{%battle3}}.[[rule_id]]',
                    'count' => 'COUNT(*)',
                ])
                ->from('{{%battle3}}')
                ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
                ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                ->andWhere([
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                    '{{%result3}}.[[key]]' => 'win',
                ])
                ->andWhere(['and',
                    ['<>', '{{%lobby3}}.[[key]]', 'private'],
                    ['<>', '{{%rule3}}.[[key]]', 'tricolor'],
                ])
                ->groupBy([
                    '{{%battle3}}.[[user_id]]',
                    '{{%battle3}}.[[rule_id]]',
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return true;
    }

    private static function updateBadgeProgressSpecials(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        UserBadge3Special::deleteAll(['user_id' => $user->id]);
        $sql = vsprintf('INSERT INTO %s ([[user_id]], [[special_id]], [[count]]) %s', [
            $db->quoteTableName(UserBadge3Special::tableName()),
            (new Query())
                ->select([
                    'user_id' => '{{%battle3}}.[[user_id]]',
                    'special_id' => '{{%weapon3}}.[[special_id]]',
                    'count' => 'COUNT(*)',
                ])
                ->from('{{%battle3}}')
                ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                ->innerJoin('{{%weapon3}}', '{{%battle3}}.[[weapon_id]] = {{%weapon3}}.[[id]]')
                ->innerJoin('{{%special3}}', '{{%weapon3}}.[[special_id]] = {{%special3}}.[[id]]')
                ->andWhere([
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                    '{{%result3}}.[[key]]' => 'win',
                ])
                ->andWhere(['and',
                    ['<>', '{{%lobby3}}.[[key]]', 'private'],
                    ['>', '{{%battle3}}.[[special]]', 0],
                ])
                ->groupBy([
                    '{{%battle3}}.[[user_id]]',
                    '{{%weapon3}}.[[special_id]]',
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return true;
    }

    private static function updateBadgeProgressTricolor(
        Connection $db,
        User $user,
        DateTimeImmutable $now,
    ): bool {
        UserBadge3Tricolor::deleteAll(['user_id' => $user->id]);
        $sql = vsprintf('INSERT INTO %s ([[user_id]], [[role_id]], [[count]]) %s', [
            $db->quoteTableName(UserBadge3Tricolor::tableName()),
            (new Query())
                ->select([
                    'user_id' => '{{%battle3}}.[[user_id]]',
                    'role_id' => '{{%battle3}}.[[our_team_role_id]]',
                    'count' => 'COUNT(*)',
                ])
                ->from('{{%battle3}}')
                ->innerJoin('{{%lobby3}}', '{{%battle3}}.[[lobby_id]] = {{%lobby3}}.[[id]]')
                ->innerJoin('{{%result3}}', '{{%battle3}}.[[result_id]] = {{%result3}}.[[id]]')
                ->innerJoin('{{%rule3}}', '{{%battle3}}.[[rule_id]] = {{%rule3}}.[[id]]')
                ->innerJoin(
                    '{{%tricolor_role3}}',
                    '{{%battle3}}.[[our_team_role_id]] = {{%tricolor_role3}}.[[id]]',
                )
                ->andWhere([
                    '{{%battle3}}.[[is_deleted]]' => false,
                    '{{%battle3}}.[[user_id]]' => $user->id,
                    '{{%lobby3}}.[[key]]' => 'splatfest_open',
                    '{{%result3}}.[[key]]' => 'win',
                    '{{%rule3}}.[[key]]' => 'tricolor',
                ])
                ->groupBy([
                    '{{%battle3}}.[[user_id]]',
                    '{{%battle3}}.[[our_team_role_id]]',
                ])
                ->createCommand($db)
                ->rawSql,
        ]);
        $db->createCommand($sql)->execute();

        return true;
    }
}

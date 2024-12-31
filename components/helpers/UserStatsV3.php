<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\userStatsV3\BadgeProgressTrait;
use app\components\helpers\userStatsV3\StatsTrait;
use app\components\helpers\userStatsV3\WeaponTrait;
use app\components\helpers\userStatsV3\XStatsTrait;
use app\models\User;
use yii\db\Connection;
use yii\db\Transaction;

final class UserStatsV3
{
    use BadgeProgressTrait;
    use StatsTrait;
    use WeaponTrait;
    use XStatsTrait;

    public static function create(User $user): bool
    {
        $now = new DateTimeImmutable('now', new DateTimeZone('Etc/UTC'));
        return Yii::$app->db->transaction(
            fn (Connection $db): bool => self::createImpl($db, $user, $now),
            Transaction::REPEATABLE_READ,
        );
    }

    private static function createImpl(Connection $db, User $user, DateTimeImmutable $now): bool
    {
        return self::createUserStats($db, $user, $now) &&
            self::createUserStatsX($db, $user, $now) &&
            self::createWeaponStats($db, $user, $now) &&
            self::createBadgeProgress($db, $user, $now);
    }
}

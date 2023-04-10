<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\salmonStatsV3\BadgeProgressTrait;
use app\components\helpers\salmonStatsV3\BigrunHistogramTrait;
use app\components\helpers\salmonStatsV3\BigrunTrait;
use app\components\helpers\salmonStatsV3\EggstraWorkHistogramTrait;
use app\components\helpers\salmonStatsV3\EggstraWorkTrait;
use app\components\helpers\salmonStatsV3\StatsTrait;
use app\models\User;
use yii\db\Connection;
use yii\db\Transaction;

final class SalmonStatsV3
{
    use BadgeProgressTrait;
    use BigrunHistogramTrait;
    use BigrunTrait;
    use EggstraWorkHistogramTrait;
    use EggstraWorkTrait;
    use StatsTrait;

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
            self::createBigrunStats($db, $user, $now) &&
            self::createEggstraWorkStats($db, $user, $now) &&
            self::createBadgeProgress($db, $user, $now) &&
            self::createBigrunHistogramStats($db) &&
            self::createEggstraWorkHistogramStats($db);
    }
}

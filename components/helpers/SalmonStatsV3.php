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
use app\components\helpers\salmonStatsV3\PlayedWithTrait;
use app\components\helpers\salmonStatsV3\StatsTrait;
use app\components\helpers\salmonStatsV3\UserEventTrait;
use app\components\helpers\salmonStatsV3\UserGoldenEggTrait;
use app\components\helpers\salmonStatsV3\UserSpecialTrait;
use app\components\helpers\salmonStatsV3\UserWeaponTrait;
use app\models\User;
use yii\db\Connection;
use yii\db\Transaction;

use function fwrite;

use const PHP_SAPI;
use const STDERR;

final class SalmonStatsV3
{
    use BadgeProgressTrait;
    use BigrunHistogramTrait;
    use BigrunTrait;
    use EggstraWorkHistogramTrait;
    use EggstraWorkTrait;
    use PlayedWithTrait;
    use StatsTrait;
    use UserEventTrait;
    use UserGoldenEggTrait;
    use UserSpecialTrait;
    use UserWeaponTrait;

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
        $tasks = [
            'createUserStats' => fn () => self::createUserStats($db, $user, $now),
            'createUserGoldenEggStats' => fn () => self::createUserGoldenEggStats($db, $user, $now),
            'createBigrunStats' => fn () => self::createBigrunStats($db, $user, $now),
            'createEggstraWorkStats' => fn () => self::createEggstraWorkStats($db, $user, $now),
            'createBadgeProgress' => fn () => self::createBadgeProgress($db, $user, $now),
            'createUserEventStats' => fn () => self::createUserEventStats($db, $user),
            'createUserSpecialStats' => fn () => self::createUserSpecialStats($db, $user),
            'createUserWeaponStats' => fn () => self::createUserWeaponStats($db, $user),
            'createPlayedWithStats' => fn () => self::createPlayedWithStats($db, $user),
            'createBigrunHistogramStats' => fn () => self::createBigrunHistogramStats($db, true),
            'createEggstraWorkHistogramStats' => fn () => self::createEggstraWorkHistogramStats($db),
        ];

        foreach ($tasks as $label => $task) {
            if (PHP_SAPI === 'cli') {
                fwrite(STDERR, "$label...\n");
            }

            if (!$task()) {
                return false;
            }
        }

        return true;
    }
}

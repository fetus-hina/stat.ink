<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\models\GearConfiguration3;
use app\models\Weapon3;
use app\models\Weapon3Alias;

use function array_filter;
use function array_map;
use function array_merge;
use function array_slice;
use function filter_var;
use function http_build_query;
use function implode;
use function is_int;
use function max;
use function vsprintf;

use const FILTER_VALIDATE_INT;

final class SendouInk
{
    private const UNKNOWN_KEY = 'U';

    public static function getBuildUrl3(
        ?Weapon3 $weapon,
        ?GearConfiguration3 $headgear,
        ?GearConfiguration3 $clothing,
        ?GearConfiguration3 $shoes,
    ): ?string {
        if (!$weapon || !$headgear || !$clothing || !$shoes) {
            return null;
        }

        $weaponId = self::getWeaponId3($weapon); // may returns 0 (for "bold", it's valid)
        if ($weaponId === null) {
            return null;
        }

        if (!$abilities = self::getAbilities($headgear, $clothing, $shoes)) {
            return null;
        }

        return vsprintf('https://sendou.ink/analyzer?%s', [
            http_build_query([
                'weapon' => $weaponId,
                'build' => implode(',', $abilities),
            ]),
        ]);
    }

    // Get SplatNet ID
    private static function getWeaponId3(Weapon3 $weapon): ?int
    {
        $ids = array_filter(
            array_map(
                function (Weapon3Alias $alias): ?int {
                    $id = filter_var($alias->key, FILTER_VALIDATE_INT);
                    return is_int($id) ? $id : null;
                },
                $weapon->weapon3Aliases,
            ),
            fn (?int $value): bool => is_int($value),
        );

        return $ids ? max($ids) : null;
    }

    private static function getAbilities(GearConfiguration3 ...$gears): array
    {
        $results = [];
        foreach ($gears as $gear) {
            $tmp = [];
            $tmp[] = $gear->ability?->sendouink ?? self::UNKNOWN_KEY;
            foreach ($gear->gearConfigurationSecondary3s as $secondary) {
                $tmp[] = $secondary->ability?->sendouink ?? self::UNKNOWN_KEY;
            }
            $results = array_merge(
                $results,
                array_slice(
                    array_merge(
                        $tmp,
                        [self::UNKNOWN_KEY, self::UNKNOWN_KEY, self::UNKNOWN_KEY, self::UNKNOWN_KEY],
                    ),
                    0,
                    4,
                ),
            );
        }

        return $results;
    }
}

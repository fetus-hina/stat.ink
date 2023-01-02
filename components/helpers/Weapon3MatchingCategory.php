<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\models\Weapon3;

use function in_array;

// https://twitter.com/sabot33n/status/1599075575272210433
final class Weapon3MatchingCategory
{
    public const CATEGORY_LONG_CHARGER = 'A';
    public const CATEGORY_MID_CHARGER = 'B';
    public const CATEGORY_LONG = 'C';
    public const CATEGORY_MID = 'D';
    public const CATEGORY_SHORT = 'E';
    public const CATEGORY_LONG_BLASTER = 'F';
    public const CATEGORY_SHORT_BLASTER = 'G';
    public const CATEGORY_LONG_ROLLER = 'H';
    public const CATEGORY_SHORT_ROLLER = 'I';

    public static function getCategory(?Weapon3 $model): ?string
    {
        $key = $model?->key;
        if (!$key) {
            return null;
        }

        foreach (self::getData() as $category => $keys) {
            if (in_array($key, $keys, true)) {
                return $category;
            }
        }

        return null;
    }

    private static function getData(): array
    {
        return [
            self::CATEGORY_LONG_CHARGER => [
                'liter4k',
                'liter4k_scope',
                'splatcharger',
                'splatscope',
            ],
            self::CATEGORY_MID_CHARGER => [
                'bamboo14mk1',
                'rpen_5h',
                'soytuber',
                'squiclean_a',
            ],
            self::CATEGORY_LONG => [
                'barrelspinner',
                'hydra',
                'kugelschreiber',
                'tristringer',
            ],
            self::CATEGORY_MID => [
                '96gal',
                'bottlegeyser',
                'dualsweeper',
                'h3reelgun',
                'jetsweeper',
                'kelvin525',
                'lact450',
                'nautilus47',
                'prime',
                'prime_collabo',
                'spaceshooter',
                'splatspinner',
                'splatspinner_collabo',
            ],
            self::CATEGORY_SHORT => [
                '52gal',
                'bold',
                'heroshooter_replica',
                'l3reelgun',
                'maneuver',
                'momiji',
                'nzap85',
                'promodeler_mg',
                'promodeler_rg',
                'quadhopper_black',
                'sharp',
                'sputtery',
                'sputtery_hue',
                'sshooter',
                'sshooter_collabo',
                'wakaba',
            ],
            self::CATEGORY_LONG_BLASTER => [
                'explosher',
                'furo',
                'longblaster',
                'rapid',
                'rapid_elite',
            ],
            self::CATEGORY_SHORT_BLASTER => [
                'bucketslosher',
                'bucketslosher_deco',
                'clashblaster',
                'hissen',
                'hotblaster',
                'nova',
                'nova_neo',
                'screwslosher',
            ],
            self::CATEGORY_LONG_ROLLER => [
                'campingshelter',
                'dynamo',
                'jimuwiper',
                'variableroller',
                'wideroller',
            ],
            self::CATEGORY_SHORT_ROLLER => [
                'carbon',
                'carbon_deco',
                'drivewiper',
                'hokusai',
                'pablo',
                'pablo_hue',
                'parashelter',
                'splatroller',
                'spygadget',
            ],
        ];
    }
}

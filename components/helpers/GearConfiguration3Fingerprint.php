<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use app\models\Ability3;
use jp3cki\uuid\Uuid;
use yii\helpers\Json;

use function array_map;
use function array_slice;
use function array_values;
use function count;

final class GearConfiguration3Fingerprint
{
    private const NS_UUID = '7b4f7ada-5b54-11ed-b954-7085c2ac6926';

    /**
     * @param null $gear
     * @param array<Ability3|null> $secondaries
     */
    public static function calc($gear, ?Ability3 $primary, array $secondaries): string
    {
        $data = self::makeData(null, $primary, $secondaries);
        return (string)Uuid::v5(self::NS_UUID, Json::encode($data, 320));
    }

    /**
     * @param null $gear
     * @param array<Ability3|null> $secondaries
     */
    private static function makeData($gear, ?Ability3 $primary, array $secondaries): array
    {
        $secondaries = array_values($secondaries);
        if (count($secondaries) > 3) {
            $secondaries = array_slice($secondaries, 0, 3);
        }

        return [
            'gear' => null,
            'primary' => $primary ? $primary->key : null,
            'secondary' => array_map(
                fn (?Ability3 $secondary): ?string => $secondary ? $secondary->key : null,
                $secondaries,
            ),
        ];
    }
}

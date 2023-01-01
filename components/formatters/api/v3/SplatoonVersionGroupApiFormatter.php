<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\formatters\api\v3;

use app\models\SplatoonVersion3;
use app\models\SplatoonVersionGroup3;

use function array_map;
use function strcmp;
use function usort;
use function version_compare;

final class SplatoonVersionGroupApiFormatter
{
    public static function toJson(
        ?SplatoonVersionGroup3 $model,
        bool $fullTranslate = false,
        bool $expandVersions = false
    ): ?array {
        if (!$model) {
            return null;
        }

        $versions = $expandVersions ? $model->splatoonVersion3s : [];
        usort(
            $versions,
            fn (SplatoonVersion3 $a, SplatoonVersion3 $b): int => version_compare($b->tag, $a->tag)
                ?: strcmp($b->tag, $a->tag)
                ?: $b->id <=> $a->id
        );

        return [
            'tag' => $model->tag,
            'name' => NameApiFormatter::toJson($model->name, 'app-version3', $fullTranslate),
            'versions' => $expandVersions
                ? array_map(
                    fn (SplatoonVersion3 $version): array => SplatoonVersionApiFormatter::toJson(
                        $version,
                        $fullTranslate,
                    ),
                    $versions,
                )
                : false,
        ];
    }
}

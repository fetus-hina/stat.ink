<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\dlStats3;

use TypeError;

use function array_map;
use function array_values;
use function gettype;
use function implode;
use function is_float;
use function is_int;
use function is_string;
use function sprintf;
use function str_contains;
use function str_replace;
use function vsprintf;

trait CsvUtilTrait
{
    /**
     * @param (string|int|float|null)[] $cols
     */
    private static function csvRow(array $cols): string
    {
        $cols = self::csvNormalizeColumns($cols);

        return implode(
            ',',
            array_map(
                fn (string $col): string => self::isCsvNeedQuote($col)
                    ? sprintf('"%s"', str_replace('"', '""', $col))
                    : $col,
                $cols,
            ),
        );
    }

    private static function isCsvNeedQuote(string $value): bool
    {
        return str_contains($value, ',') || str_contains($value, '"') || str_contains($value, "\n");
    }

    /**
     * @param (string|int|float|null)[] $cols
     * @return string[]
     */
    private static function csvNormalizeColumns(array $cols): array
    {
        return array_values(
            array_map(
                function ($value): string {
                    if (
                        $value === null ||
                        is_string($value) ||
                        is_int($value) ||
                        is_float($value)
                    ) {
                        return (string)$value;
                    } else {
                        throw new TypeError(
                            vsprintf('csvRow: unexpected column type %s', [
                                gettype($value),
                            ]),
                        );
                    }
                },
                $cols,
            ),
        );
    }
}

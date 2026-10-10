<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\i18n;

use function array_key_exists;

/**
 * Finds translation calls whose message does not exist in the catalog
 */
final class TranslationKeyChecker
{
    public const REASON_MISSING_KEY = 'missing_key';
    public const REASON_MISSING_CATALOG = 'missing_catalog';

    /**
     * @template T of array{category: string, message: string}
     * @param iterable<T> $calls
     * @param array<string, array<string, string>|null> $catalogs
     *   Catalogs keyed by category. `null` means the catalog file does not
     *   exist. Categories not in this map are not checked (e.g., "yii").
     * @return list<T&array{reason: string}>
     */
    public static function findMissing(iterable $calls, array $catalogs): array
    {
        $missing = [];
        foreach ($calls as $call) {
            if (!array_key_exists($call['category'], $catalogs)) {
                continue;
            }

            $catalog = $catalogs[$call['category']];
            if ($catalog === null) {
                $missing[] = $call + ['reason' => self::REASON_MISSING_CATALOG];
            } elseif (!array_key_exists($call['message'], $catalog)) {
                $missing[] = $call + ['reason' => self::REASON_MISSING_KEY];
            }
        }
        return $missing;
    }
}

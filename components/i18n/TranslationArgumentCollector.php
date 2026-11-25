<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\i18n;

use function array_intersect;
use function array_values;

/**
 * Collects the names of the params passed to each message by its callers
 */
final class TranslationArgumentCollector
{
    /**
     * Returns the params passed by all calls of each message
     *
     * If any call passes params that cannot be determined statically, no
     * params are taken for the message. Likewise, no params are taken for the
     * messages of a category that is called with a non-literal message (e.g.,
     * `Yii::t('app', $message, [...])`), as it might call any of them.
     *
     * Calls whose category is not a literal either cannot be taken into
     * account; it would leave no params for any message.
     *
     * @param iterable<array{category: string, message: string, arguments: list<string>|null, ...}> $calls
     * @param iterable<array{category: string|null, ...}> $dynamicCalls
     * @return array<string, array<string, list<string>>> category => message => names
     */
    public static function collect(iterable $calls, iterable $dynamicCalls = []): array
    {
        $dynamicCategories = [];
        foreach ($dynamicCalls as $call) {
            if ($call['category'] !== null) {
                $dynamicCategories[$call['category']] = true;
            }
        }

        $results = [];
        foreach ($calls as $call) {
            $category = $call['category'];
            $message = $call['message'];
            $arguments = isset($dynamicCategories[$category]) ? [] : ($call['arguments'] ?? []);

            $results[$category][$message] = isset($results[$category][$message])
                ? array_values(array_intersect($results[$category][$message], $arguments))
                : $arguments;
        }
        return $results;
    }
}

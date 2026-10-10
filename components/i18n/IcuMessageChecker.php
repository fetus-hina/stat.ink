<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\i18n;

use MessageFormatter;
use Throwable;

use function array_intersect;
use function array_unique;
use function in_array;
use function intl_get_error_message;
use function is_int;
use function preg_match;
use function preg_match_all;
use function sprintf;
use function str_contains;
use function str_starts_with;

use const PREG_SET_ORDER;

/**
 * Checks that a translation keeps the ICU placeholders of its source message
 *
 * Both messages are actually formatted with a distinct value for each argument.
 * A value shown by the source but not by the translation is reported as missing.
 */
final class IcuMessageChecker
{
    public const INVALID_PATTERN = 'invalid_pattern';
    public const MISSING_ARGUMENT = 'missing_argument';
    public const UNRESOLVED_PLACEHOLDER = 'unresolved_placeholder';

    /**
     * Arguments that translations may drop intentionally
     */
    private const IGNORED_ARGUMENTS = [
        'boy', // gender marker of Salmon Run titles
        'girl',
    ];
    private const IGNORED_ARGUMENT_PREFIX = 'translate_hint_';

    private const NUMERIC_TYPES = [
        'duration',
        'number',
        'ordinal',
        'plural',
        'selectordinal',
        'spellout',
    ];
    private const DATE_TYPES = ['date', 'time'];
    private const DATE_VALUE = 1700000000;

    /**
     * @return list<array{type: string, detail: string}>
     */
    public static function check(string $source, string $translation, string $locale): array
    {
        try {
            $translationFormatter = new MessageFormatter($locale, $translation);
        } catch (Throwable) {
            return [self::issue(self::INVALID_PATTERN, intl_get_error_message())];
        }

        try {
            $sourceFormatter = new MessageFormatter('en_US', $source);
        } catch (Throwable) {
            return []; // The source is not an ICU message; nothing to compare
        }

        $params = self::createParams($source, $translation);
        $sourceText = (string)$sourceFormatter->format($params);
        $translationText = $translationFormatter->format($params);
        if ($translationText === false) {
            return [self::issue(self::INVALID_PATTERN, intl_get_error_message())];
        }

        $issues = [];
        if (self::hasPlaceholder($translationText) && !self::hasPlaceholder($sourceText)) {
            $issues[] = self::issue(self::UNRESOLVED_PLACEHOLDER, $translationText);
        }

        foreach ($params as $name => $value) {
            if (self::isIgnored((string)$name) || $value === self::DATE_VALUE || $value === 'other') {
                continue;
            }

            $rendered = (string)$value;
            if (str_contains($sourceText, $rendered) && !str_contains($translationText, $rendered)) {
                $issues[] = self::issue(
                    self::MISSING_ARGUMENT,
                    sprintf(is_int($value) ? 'The number of {%s} is not shown' : '{%s} is not shown', $name),
                );
            }
        }

        return $issues;
    }

    /**
     * Creates a distinct value for each (possible) argument of the source
     *
     * Arguments only in the translation get no value, so they remain unresolved.
     * Plural branches like `=1{battle}` are also taken as arguments, but it is
     * harmless; such values are never shown.
     *
     * @return array<string, int|string>
     */
    private static function createParams(string $source, string $translation): array
    {
        preg_match_all('/\{\s*(\w+)\s*[,}]/', $source, $matches);
        preg_match_all('/\{\s*(\w+)\s*,\s*([a-z]+)/', $source . "\n" . $translation, $typeMatches, PREG_SET_ORDER);

        $types = [];
        foreach ($typeMatches as [, $name, $type]) {
            $types[$name][] = $type;
        }

        $params = [];
        $numberIndex = 0;
        foreach (array_unique($matches[1]) as $name) {
            $nameTypes = $types[$name] ?? [];
            $params[$name] = match (true) {
                (bool)array_intersect($nameTypes, self::DATE_TYPES) => self::DATE_VALUE,
                (bool)array_intersect($nameTypes, self::NUMERIC_TYPES) => 37 + 4 * $numberIndex++,
                in_array('select', $nameTypes, true) => 'other',
                default => "\u{27E6}{$name}\u{27E7}",
            };
        }
        return $params;
    }

    private static function hasPlaceholder(string $text): bool
    {
        return (bool)preg_match('/\{\s*\w/', $text);
    }

    private static function isIgnored(string $name): bool
    {
        return in_array($name, self::IGNORED_ARGUMENTS, true) ||
            str_starts_with($name, self::IGNORED_ARGUMENT_PREFIX);
    }

    /**
     * @return array{type: string, detail: string}
     */
    private static function issue(string $type, string $detail): array
    {
        return ['type' => $type, 'detail' => $detail];
    }
}

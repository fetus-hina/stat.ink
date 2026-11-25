<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\i18n;

use MessageFormatter;
use NumberFormatter;
use Throwable;

use function array_intersect;
use function array_unique;
use function array_values;
use function count;
use function implode;
use function in_array;
use function intdiv;
use function intl_get_error_message;
use function is_string;
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
 * Arguments for plural forms are formatted with each of the values covering all
 * plural categories, so that every branch is checked.
 *
 * `$extraArguments` lists the arguments the caller passes in addition to those
 * in the source (e.g., a raw number for the plural form of a formatted value).
 * Translations may use them, but are not required to.
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

    private const SOURCE_LOCALE = 'en_US';

    private const PLURAL_TYPES = ['plural', 'selectordinal'];

    /**
     * Values covering all plural categories of the supported languages
     *
     * PHP intl does not expose the samples of CLDR plural rules, so they are fixed here.
     * E.g., ru selects "one" for 1/21/101, "few" for 2/3/22, "many" for 0/5/11/37,
     * and "other" for 1.5; fr selects "one" for 0/1/1.5 and "many" for 1000000.
     */
    private const PLURAL_VALUES = [0, 1, 2, 3, 5, 11, 21, 22, 37, 101, 1.5, 1000000];

    private const NUMERIC_TYPES = [
        'duration',
        'number',
        'ordinal',
        'spellout',
    ];

    /**
     * Digits for the values of the other numeric arguments
     *
     * They are not in `PLURAL_VALUES`, so that the values are not mistaken for
     * each other in the formatted text.
     */
    private const NUMBER_DIGITS = ['4', '6', '8', '9'];
    private const DATE_TYPES = ['date', 'time'];
    private const DATE_VALUE = 1700000000;

    /**
     * @param list<string> $extraArguments
     * @return list<array{type: string, detail: string}>
     */
    public static function check(
        string $source,
        string $translation,
        string $locale,
        array $extraArguments = [],
    ): array {
        try {
            $translationFormatter = new MessageFormatter($locale, $translation);
        } catch (Throwable) {
            return [self::issue(self::INVALID_PATTERN, intl_get_error_message())];
        }

        try {
            $sourceFormatter = new MessageFormatter(self::SOURCE_LOCALE, $source);
        } catch (Throwable) {
            return []; // The source is not an ICU message; nothing to compare
        }

        [$params, $pluralNames] = self::createParams($source, $translation, $extraArguments);
        $sourceNumberFormatters = self::createNumberFormatters(self::SOURCE_LOCALE);
        $translationNumberFormatters = self::createNumberFormatters($locale);

        $issues = [];
        foreach (self::createPluralValueSets($pluralNames) as $pluralValues) {
            $currentParams = $params + $pluralValues; // Keep integer keys like {1}
            $sourceText = (string)$sourceFormatter->format($currentParams);
            $translationText = $translationFormatter->format($currentParams);
            if ($translationText === false) {
                return [self::issue(self::INVALID_PATTERN, intl_get_error_message())];
            }

            $suffix = self::describeValues($pluralValues);
            if (self::hasPlaceholder($translationText) && !self::hasPlaceholder($sourceText)) {
                $issues[self::UNRESOLVED_PLACEHOLDER] ??= self::issue(
                    self::UNRESOLVED_PLACEHOLDER,
                    $translationText . $suffix,
                );
            }

            foreach ($currentParams as $name => $value) {
                if (self::isIgnored((string)$name) || $value === self::DATE_VALUE || $value === 'other') {
                    continue;
                }

                if (
                    self::isShown($sourceText, $value, $sourceNumberFormatters) &&
                    !self::isShown($translationText, $value, $translationNumberFormatters)
                ) {
                    $issues[self::MISSING_ARGUMENT . ':' . $name] ??= self::issue(
                        self::MISSING_ARGUMENT,
                        sprintf(is_string($value) ? '{%s} is not shown' : 'The number of {%s} is not shown', $name) .
                            $suffix,
                    );
                }
            }
        }

        return array_values($issues);
    }

    /**
     * Creates a distinct value for each (possible) argument of the source
     *
     * Arguments only in the translation get no value, so they remain unresolved,
     * unless they are listed in `$extraArguments`.
     * Plural branches like `=1{battle}` are also taken as arguments, but it is
     * harmless; such values are never shown.
     *
     * Arguments used for plural forms are returned separately, since they take
     * each of `PLURAL_VALUES` in turn.
     *
     * @param list<string> $extraArguments
     * @return array{array<string, int|string>, list<string>}
     */
    private static function createParams(string $source, string $translation, array $extraArguments): array
    {
        preg_match_all('/\{\s*(\w+)\s*[,}]/', $source, $matches);
        preg_match_all('/\{\s*(\w+)\s*,\s*([a-z]+)/', $source . "\n" . $translation, $typeMatches, PREG_SET_ORDER);

        $types = [];
        foreach ($typeMatches as [, $name, $type]) {
            $types[$name][] = $type;
        }

        $params = [];
        $pluralNames = [];
        $numberIndex = 0;
        $names = array_unique([...$matches[1], ...$extraArguments]);
        foreach ($names as $name) {
            $nameTypes = $types[$name] ?? [];
            if (array_intersect($nameTypes, self::PLURAL_TYPES)) {
                $pluralNames[] = $name;
                continue;
            }

            $params[$name] = match (true) {
                (bool)array_intersect($nameTypes, self::DATE_TYPES) => self::DATE_VALUE,
                (bool)array_intersect($nameTypes, self::NUMERIC_TYPES) => self::createNumber(
                    $numberIndex++,
                    count($names),
                ),
                in_array('select', $nameTypes, true) => 'other',
                default => "\u{27E6}{$name}\u{27E7}",
            };
        }
        return [$params, $pluralNames];
    }

    /**
     * Creates the `$index`-th number with the digits of `NUMBER_DIGITS`
     *
     * All the numbers have the same number of digits enough for `$count` values,
     * so that none of them is a part of another.
     */
    private static function createNumber(int $index, int $count): int
    {
        $base = count(self::NUMBER_DIGITS);
        $digits = '';
        for ($width = 0, $limit = 1; $width < 2 || $limit < $count; ++$width, $limit *= $base) {
            $digits = self::NUMBER_DIGITS[$index % $base] . $digits;
            $index = intdiv($index, $base);
        }
        return (int)$digits;
    }

    /**
     * Creates all combinations of `PLURAL_VALUES` for the plural arguments
     *
     * @param list<string> $names
     * @return list<array<string, int|float>>
     */
    private static function createPluralValueSets(array $names): array
    {
        $sets = [[]];
        foreach ($names as $name) {
            $newSets = [];
            foreach ($sets as $set) {
                foreach (self::PLURAL_VALUES as $value) {
                    $set[$name] = $value;
                    $newSets[] = $set;
                }
            }
            $sets = $newSets;
        }
        return $sets;
    }

    /**
     * @param array<string, int|float> $values
     */
    private static function describeValues(array $values): string
    {
        if (!$values) {
            return '';
        }

        $list = [];
        foreach ($values as $name => $value) {
            $list[] = "{$name} = {$value}";
        }
        return ' (' . implode(', ', $list) . ')';
    }

    /**
     * Creates formatters for the ways numbers are shown
     *
     * Numbers are rendered with the locale format by `{n, number}` or `#`.
     * Decimals may also be rounded by an integer format like `{n, number, integer}`.
     *
     * @return list<NumberFormatter>
     */
    private static function createNumberFormatters(string $locale): array
    {
        $integerFormatter = new NumberFormatter($locale, NumberFormatter::DECIMAL);
        $integerFormatter->setAttribute(NumberFormatter::MAX_FRACTION_DIGITS, 0);
        return [new NumberFormatter($locale, NumberFormatter::DECIMAL), $integerFormatter];
    }

    /**
     * Numbers are shown as is by `{n}`, or formatted by one of `$numberFormatters`
     *
     * @param list<NumberFormatter> $numberFormatters
     */
    private static function isShown(string $text, int|float|string $value, array $numberFormatters): bool
    {
        if (str_contains($text, (string)$value)) {
            return true;
        }

        if (is_string($value)) {
            return false;
        }

        foreach ($numberFormatters as $numberFormatter) {
            $formatted = $numberFormatter->format($value);
            if ($formatted !== false && str_contains($text, $formatted)) {
                return true;
            }
        }
        return false;
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

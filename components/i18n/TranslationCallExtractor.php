<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\i18n;

use PhpToken;

use function chr;
use function count;
use function in_array;
use function intval;
use function is_int;
use function max;
use function mb_chr;
use function ord;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function str_replace;
use function strcasecmp;
use function stripos;
use function strrpos;
use function substr;

use const T_ARRAY;
use const T_COMMENT;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_CURLY_OPEN;
use const T_DOC_COMMENT;
use const T_DOLLAR_OPEN_CURLY_BRACES;
use const T_DOUBLE_ARROW;
use const T_DOUBLE_COLON;
use const T_ELLIPSIS;
use const T_LNUMBER;
use const T_NAME_FULLY_QUALIFIED;
use const T_NAME_QUALIFIED;
use const T_NULLSAFE_OBJECT_OPERATOR;
use const T_OBJECT_OPERATOR;
use const T_STRING;
use const T_WHITESPACE;

/**
 * Extracts translation calls (e.g., `Yii::t('category', 'message')`) from PHP source code
 */
final class TranslationCallExtractor
{
    /**
     * @return array{
     *   calls: list<array{category: string, message: string, line: int, arguments: list<string>|null}>,
     *   dynamic: list<array{line: int, category: string|null}>,
     * }
     *
     * `calls` are calls whose category and message are both string literals.
     * `arguments` are the names of the params passed to the call (the keys of
     * the array literal; "0", "1", ... for positional ones), or null if they
     * cannot be determined statically (e.g., a variable is passed).
     * `dynamic` lists the calls that cannot be checked statically (e.g., a
     * variable or a concatenation is passed), with the category if it is a
     * string literal.
     */
    public static function extract(string $code): array
    {
        $tokens = self::significantTokens(PhpToken::tokenize($code));
        $calls = [];
        $dynamic = [];

        $n = count($tokens);
        for ($i = 0; $i + 2 < $n; ++$i) {
            $open = self::findCallOpenParen($tokens, $i);
            if ($open === null) {
                continue;
            }

            $category = self::parseLiteralArgument($tokens, $open + 1, [',']);
            $message = $category
                ? self::parseLiteralArgument($tokens, $category['next'] + 1, [',', ')'])
                : null;
            if ($category && $message) {
                $calls[] = [
                    'category' => $category['value'],
                    'message' => $message['value'],
                    'line' => $tokens[$i]->line,
                    'arguments' => $tokens[$message['next']]->text === ')'
                        ? []
                        : self::parseArgumentNames($tokens, $message['next'] + 1),
                ];
            } else {
                $dynamic[] = [
                    'line' => $tokens[$i]->line,
                    'category' => $category['value'] ?? null,
                ];
            }
        }

        return [
            'calls' => $calls,
            'dynamic' => $dynamic,
        ];
    }

    /**
     * Returns the index of "(" if a translation call starts at $i
     *
     * Recognized calls:
     *   - Yii::t(...)
     *   - ...->translate(...), ...?->translate(...) (yii\i18n\I18N::translate())
     *   - Translator::translate*(...) (app\components\helpers\Translator)
     *
     * @param list<PhpToken> $tokens
     */
    private static function findCallOpenParen(array $tokens, int $i): ?int
    {
        $t0 = $tokens[$i];
        $t1 = $tokens[$i + 1] ?? null;
        $t2 = $tokens[$i + 2] ?? null;
        $t3 = $tokens[$i + 3] ?? null;

        // ->translate( / ?->translate(
        if (
            $t0->is([T_OBJECT_OPERATOR, T_NULLSAFE_OBJECT_OPERATOR]) &&
            $t1?->is(T_STRING) &&
            strcasecmp($t1->text, 'translate') === 0 &&
            $t2?->text === '('
        ) {
            return $i + 2;
        }

        if (!$t1?->is(T_DOUBLE_COLON) || !$t2?->is(T_STRING) || $t3?->text !== '(') {
            return null;
        }

        // Yii::t(
        if (self::isClassName($t0, 'Yii') && strcasecmp($t2->text, 't') === 0) {
            return $i + 3;
        }

        // Translator::translate*(
        if (self::isClassName($t0, 'Translator') && stripos($t2->text, 'translate') === 0) {
            return $i + 3;
        }

        return null;
    }

    private static function isClassName(PhpToken $token, string $shortName): bool
    {
        if ($token->is(T_STRING)) {
            return strcasecmp($token->text, $shortName) === 0;
        }

        if ($token->is([T_NAME_FULLY_QUALIFIED, T_NAME_QUALIFIED])) {
            $pos = strrpos($token->text, '\\');
            return strcasecmp(substr($token->text, $pos === false ? 0 : $pos + 1), $shortName) === 0;
        }

        return false;
    }

    /**
     * Parses an argument made of string literals joined with "."
     *
     * @param list<PhpToken> $tokens
     * @param list<string> $terminators
     * @return array{value: string, next: int}|null
     *   `next` is the index of the terminator token
     */
    private static function parseLiteralArgument(array $tokens, int $i, array $terminators): ?array
    {
        $value = '';
        while (true) {
            $token = $tokens[$i] ?? null;
            if (!$token?->is(T_CONSTANT_ENCAPSED_STRING)) {
                return null;
            }
            $value .= self::decodeLiteral($token->text);

            $next = $tokens[$i + 1] ?? null;
            if (in_array($next?->text, $terminators, true)) {
                return ['value' => $value, 'next' => $i + 1];
            }
            if ($next?->text !== '.') {
                return null;
            }
            $i += 2;
        }
    }

    /**
     * Parses the names of the params passed as an array literal
     *
     * @param list<PhpToken> $tokens
     * @param int $i The index of the token next to "," after the message
     * @return list<string>|null
     */
    private static function parseArgumentNames(array $tokens, int $i): ?array
    {
        $token = $tokens[$i] ?? null;
        if ($token?->text === ')') {
            return []; // trailing comma
        }

        if ($token?->text === '[') {
            $close = ']';
            $j = $i + 1;
        } elseif ($token?->is(T_ARRAY) && ($tokens[$i + 1] ?? null)?->text === '(') {
            $close = ')';
            $j = $i + 2;
        } else {
            return null;
        }

        $names = [];
        $position = 0;
        while (($tokens[$j] ?? null)?->text !== $close) {
            if (($tokens[$j] ?? null)?->is(T_ELLIPSIS)) {
                return null;
            }

            $element = self::findElementEnd($tokens, $j, $close);
            if ($element === null) {
                return null;
            }

            if ($element['arrow'] === null) {
                $names[] = (string)$position++;
            } else {
                $key = $element['arrow'] === $j + 1 ? self::parseArrayKey($tokens[$j]) : null;
                if ($key === null) {
                    return null;
                }

                $names[] = (string)$key;
                if (is_int($key)) {
                    $position = max($position, $key + 1);
                }
            }

            $j = $element['end'];
            if ($tokens[$j]->text === ',') {
                ++$j;
            }
        }

        // The array must be the whole argument (e.g., not `[...] + $params`)
        return in_array(($tokens[$j + 1] ?? null)?->text, [',', ')'], true) ? $names : null;
    }

    /**
     * Parses a literal array key as PHP does (e.g., "5" is an integer key)
     */
    private static function parseArrayKey(PhpToken $token): int|string|null
    {
        if ($token->is(T_LNUMBER)) {
            // intval() does not accept "_" (1_000) and "0o" (0o17)
            return intval((string)preg_replace('/\A0[oO]/', '0', str_replace('_', '', $token->text)), 0);
        }

        if ($token->is(T_CONSTANT_ENCAPSED_STRING)) {
            $key = self::decodeLiteral($token->text);
            return preg_match('/\A(?:0|-?[1-9][0-9]*)\z/', $key) ? (int)$key : $key;
        }

        return null;
    }

    /**
     * Finds the end of an array element starting at $i
     *
     * @param list<PhpToken> $tokens
     * @return array{end: int, arrow: int|null}|null
     *   `end` is the index of "," or the closing bracket after the element;
     *   `arrow` is the index of the first "=>" at the top level of the element
     */
    private static function findElementEnd(array $tokens, int $i, string $close): ?array
    {
        $depth = 0;
        $arrow = null;
        for ($n = count($tokens); $i < $n; ++$i) {
            $token = $tokens[$i];
            if ($depth === 0 && (self::isChar($token, ',') || self::isChar($token, $close))) {
                return ['end' => $i, 'arrow' => $arrow];
            }

            if (
                self::isChar($token, '(') ||
                self::isChar($token, '[') ||
                self::isChar($token, '{') ||
                $token->is([T_CURLY_OPEN, T_DOLLAR_OPEN_CURLY_BRACES]) // "{$x}", "${x}"
            ) {
                ++$depth;
            } elseif (self::isChar($token, ')') || self::isChar($token, ']') || self::isChar($token, '}')) {
                if (--$depth < 0) {
                    return null;
                }
            } elseif ($depth === 0 && $arrow === null && $token->is(T_DOUBLE_ARROW)) {
                $arrow = $i;
            }
        }

        return null;
    }

    /**
     * Whether the token is the single character, not a part of a string (e.g., "a,b")
     */
    private static function isChar(PhpToken $token, string $char): bool
    {
        return $token->id === ord($char);
    }

    /**
     * @param PhpToken[] $tokens
     * @return list<PhpToken>
     */
    private static function significantTokens(array $tokens): array
    {
        $result = [];
        foreach ($tokens as $token) {
            if (!$token->is([T_WHITESPACE, T_COMMENT, T_DOC_COMMENT])) {
                $result[] = $token;
            }
        }
        return $result;
    }

    /**
     * Decodes a T_CONSTANT_ENCAPSED_STRING token (no interpolation inside)
     */
    private static function decodeLiteral(string $literal): string
    {
        $body = substr($literal, 1, -1);
        if ($literal[0] === "'") {
            return (string)preg_replace_callback(
                '/\\\\([\\\\\'])/',
                fn (array $m): string => $m[1],
                $body,
            );
        }

        return (string)preg_replace_callback(
            '/\\\\(?:([nrtvef\\\\$"])|([0-7]{1,3})|x([0-9A-Fa-f]{1,2})|u\{([0-9A-Fa-f]+)\})/',
            fn (array $m): string => match (true) {
                $m[1] !== '' => match ($m[1]) {
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    'v' => "\v",
                    'e' => "\e",
                    'f' => "\f",
                    default => $m[1],
                },
                $m[2] !== '' => chr(intval($m[2], 8) & 0xff),
                $m[3] !== '' => chr(intval($m[3], 16) & 0xff),
                default => (string)mb_chr(intval($m[4], 16), 'UTF-8'),
            },
            $body,
        );
    }
}

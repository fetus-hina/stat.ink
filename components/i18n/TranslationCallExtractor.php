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
use function hexdec;
use function in_array;
use function mb_chr;
use function octdec;
use function preg_replace_callback;
use function strcasecmp;
use function stripos;
use function strrpos;
use function substr;

use const T_COMMENT;
use const T_CONSTANT_ENCAPSED_STRING;
use const T_DOC_COMMENT;
use const T_DOUBLE_COLON;
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
     *   calls: list<array{category: string, message: string, line: int}>,
     *   dynamic: list<int>,
     * }
     *
     * `calls` are calls whose category and message are both string literals.
     * `dynamic` lists the lines of calls that cannot be checked statically
     * (e.g., a variable or a concatenation is passed).
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
                ];
            } else {
                $dynamic[] = $tokens[$i]->line;
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
     * @param list<PhpToken> $tokens
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
                ($m[1] ?? '') !== '' => match ($m[1]) {
                    'n' => "\n",
                    'r' => "\r",
                    't' => "\t",
                    'v' => "\v",
                    'e' => "\e",
                    'f' => "\f",
                    default => $m[1],
                },
                ($m[2] ?? '') !== '' => chr(octdec($m[2]) & 0xff),
                ($m[3] ?? '') !== '' => chr(hexdec($m[3])),
                default => (string)mb_chr(hexdec($m[4]), 'UTF-8'),
            },
            $body,
        );
    }
}

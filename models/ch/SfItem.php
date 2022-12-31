<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\ch;

use DomainException;
use LogicException;
use Yii;
use yii\base\Model;

class SfItem extends Model
{
    public $value;
    public $params = [];

    public static function create(string $text): ?self
    {
        if (!$_ = static::parseAndCreate($text)) {
            return null;
        }
        list($obj, $remains) = $_;
        return ($obj instanceof self && trim((string)$remains) === '')
            ? $obj
            : null;
    }

    public static function parseAndCreate(string $text): ?array // [?self, string]
    {
        $obj = Yii::createObject(['__class' => static::class]);
        $remains = $obj->parse($text);
        if ($remains === null) {
            return null;
        }
        return [$obj, $remains];
    }

    public function __toString()
    {
        return $this->renderBareItem($this->value) . $this->renderParameters($this->params);
    }

    protected function renderBareItem($value): string
    {
        if (is_int($value)) {
            return $this->renderSfInteger($value);
        } elseif (is_float($value)) {
            return $this->renderSfDecimal($value);
        } elseif (is_string($value)) {
            return preg_match('/\A[\x20-\x7e]+\z/', $value)
                ? $this->renderSfString($value)
                : $this->renderSfBinary($value);
        } elseif (is_bool($value)) {
            return $this->renderSfBoolean($value);
        } else {
            throw new DomainException('Unexpected value');
        }
    }

    protected function renderParameters(array $params): string
    {
        $results = [];
        foreach ($params as $key => $value) {
            if (!preg_match('/\A[a-z*][a-z0-9_.*-]*\z/', $key)) {
                throw new DomainException('Unexpected parameter key');
            }

            if ($value === null) {
                $results[] = ';' . $key;
            } else {
                $results[] = ';' . $key . '=' . $this->renderBareItem($value);
            }
        }
        return implode('', $results);
    }

    protected function renderSfInteger(int $value): string
    {
        return (string)$value;
    }

    protected function renderSfDecimal(float $value): string
    {
        // sf-decimal  = ["-"] 1*12DIGIT "." 1*3DIGIT
        if (is_nan($value)) {
            throw new DomainException('NaN is not supported');
        }
        if (is_infinite($value)) {
            throw new DomainException('INF is not supported');
        }
        return sprintf('%.3f', $value);
    }

    protected function renderSfString(string $value): string
    {
        if (!preg_match('/\A[\x20-\x7e]+\z/', $value)) {
            throw new DomainException('Unexpected value');
        }

        return vsprintf('"%s"', [
            preg_replace_callback(
                '/([\x22\x5c])/',
                fn (array $match): string => '\\' . $match[1],
                $value,
            ),
        ]);
    }

    protected function renderSfBinary(string $value): string
    {
        return ':' . base64_encode($value) . ':';
    }

    protected function renderSfBoolean(bool $value): string
    {
        return '?' . ($value ? '1' : '0');
    }

    protected function parse(string $encoded): ?string
    {
        $this->value = null;
        $this->params = [];

        // sf-item   = bare-item parameters
        // bare-item = sf-integer / sf-decimal / sf-string / sf-token
        //             / sf-binary / sf-boolean
        $sfInteger = '(?:-?[0-9]{1,15})';
        $sfDecimal = '(?:-?[0-9]{1,12}\.[0-9]{1,3})';
        $sfString = '(?:"(?:[\x20\x21\x23-\x5b\x5d-\x7e]|\x5c[\x22\x5c])*")';
        $sfBinary = '(?::[0-9a-zA-Z+\/]*=*:)';
        $sfBoolean = '(?:\x3f[01])';
        $bareItem = "(?:{$sfDecimal}|{$sfInteger}|{$sfString}|{$sfBinary}|{$sfBoolean})";
        $pname = '(?:(?:\x2a|[a-z])[a-z0-9_.*-]*)';
        $pvalue = $bareItem;
        $param = "(?:{$pname}(?:={$pvalue}?)?)";
        $params = "(?:(?:;[\\x20\\x09]*{$param})*)";
        $sfItem = "(?:(?<bareItem>{$bareItem})(?<params>{$params}))";

        $parseValue = function (string $encoded) use (
            $sfBinary,
            $sfBoolean,
            $sfDecimal,
            $sfInteger,
            $sfString
        ) {
            if (preg_match("/\\A{$sfDecimal}\\z/", $encoded, $match)) {
                return floatval($match[0]);
            } elseif (preg_match("/\\A{$sfInteger}\\z/", $encoded, $match)) {
                return intval($match[0]);
            } elseif (preg_match("/\\A{$sfString}\\z/", $encoded, $match)) {
                return preg_replace(
                    '/\x5c([\x22\x5c])/',
                    '$1',
                    substr($match[0], 1, strlen($match[0]) - 2),
                );
            } elseif (preg_match("/\\A{$sfBinary}\\z/", $encoded, $match)) {
                return base64_decode(
                    substr($match[0], 1, strlen($match[0]) - 2),
                );
            } elseif (preg_match("/\\A{$sfBoolean}\\z/", $encoded, $match)) {
                return $encoded === '?1';
            } else {
                throw new LogicException('BUG');
            }
        };

        if (
            !preg_match(
                "/\\A{$sfItem}(?<remains>.*)\\z/",
                $encoded,
                $match,
                PREG_UNMATCHED_AS_NULL,
            )
        ) {
            return null;
        }
        $paramsStr = (string)$match['params'];
        $remains = (string)$match['remains'];

        $this->value = $parseValue((string)$match['bareItem']);

        $paramsStr = ltrim($paramsStr, "; \t");
        while (strlen($paramsStr) > 0) {
            if (
                !preg_match(
                    "/\\A(?<name>{$pname})(?:=(?<value>{$pvalue}))?/",
                    $paramsStr,
                    $match,
                    PREG_UNMATCHED_AS_NULL,
                )
            ) {
                throw new LogicException('BUG');
            }

            $this->params[$match['name']] = ($match['value'] === null)
                ? null
                : $parseValue((string)$match['value']);

            $paramsStr = ltrim(substr($paramsStr, strlen($match[0])), "; \t");
        }
        ksort($this->params, SORT_STRING);

        return $remains;
    }

    public static function compare(self $a, self $b): int
    {
        return strcmp((string)$a, (string)$b);
    }
}

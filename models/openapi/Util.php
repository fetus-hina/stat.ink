<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi;

use Base32\Base32;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

trait Util
{
    public static function oapiRef(?string $className = null): array
    {
        return [
            '$ref' => vsprintf('#/components/schemas/%s', [
                call_user_func([$className ?? static::class, 'oapiRefName']),
            ]),
        ];
    }

    public static function oapiRefName(): string
    {
        $fqcn = static::class;
        $baseName = basename(str_replace('\\', DIRECTORY_SEPARATOR, $fqcn));
        $hash = substr(
            strtolower(Base32::encode(hash('sha256', $fqcn, true))),
            0,
            4
        );
        return '__' . $hash . '__' . $baseName;
    }

    public static function oapiKey(
        ?string $additionalDescription = null,
        ?array $enumValues = null,
        bool $replaceDescription = false
    ): array {
        $result = [
            'type' => 'string',
            'pattern' => '^[a-z0-9_]+$',
            'description' => $replaceDescription && $additionalDescription !== null
                ? (string)$additionalDescription
                : trim(implode("\n", [
                    Yii::t('app-apidoc1', 'Identification string for use with other API'),
                    '',
                    (string)$additionalDescription,
                ])),
        ];
        if ($enumValues) {
            $result['enum'] = $enumValues;
        }
        return $result;
    }

    public static function oapiKeyValueTable(
        string $valueLabel,
        /* string|callable */ $category,
        array $items,
        /* string|callable */ $keyColumn = 'key',
        string $valueColumn = 'name',
        ?string $keyLabelHtml = null
    ): ?string {
        if (!$items) {
            return null;
        }

        if ($keyLabelHtml === null) {
            $keyLabelHtml = Html::tag('code', Html::encode('key'));
        }

        return "<table>\n" .
            static::oapiKeyValueTableThead($keyLabelHtml, $valueLabel) . "\n" .
            static::oapiKeyValueTableTbody(
                ArrayHelper::getColumn($items, $keyColumn),
                ArrayHelper::getColumn(
                    $items,
                    function ($item) use ($category, $valueColumn): string {
                        $_category = is_callable($category)
                            ? $category($item)
                            : $category;

                        return Yii::t(
                            $_category,
                            ArrayHelper::getValue($item, $valueColumn)
                        );
                    }
                )
            ) . "\n" .
            '</table>';
    }

    private static function oapiKeyValueTableThead(string $keyLabelHtml, string $valueLabel): string
    {
        return Html::tag('thead', Html::tag('tr', implode('', [
            Html::tag('th', $keyLabelHtml),
            Html::tag('th', Html::encode($valueLabel)),
        ])));
    }

    private static function oapiKeyValueTableTbody(array $keys, array $values): string
    {
        return Html::tag('tbody', "\n" . implode("\n", array_map(
            function (string $key, string $value): string {
                return Html::tag('tr', implode('', [
                    Html::tag('td', Html::tag('code', Html::encode($key))),
                    Html::tag('td', Html::encode($value)),
                ]));
            },
            $keys,
            $values
        )) . "\n");
    }
}

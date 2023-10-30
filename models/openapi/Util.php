<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi;

use ParagonIE\ConstantTime\Base32;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function array_map;
use function basename;
use function call_user_func;
use function count;
use function hash;
use function implode;
use function is_callable;
use function str_replace;
use function substr;
use function trim;
use function vsprintf;

use const DIRECTORY_SEPARATOR;

trait Util
{
    /** @return array<string, string> */
    public static function oapiRef(?string $className = null): array
    {
        $className = $className ?? static::class;
        return [
            '$ref' => vsprintf('#/components/schemas/%s', [
                call_user_func([$className, 'oapiRefName']),
            ]),
        ];
    }

    public static function oapiRefName(): string
    {
        $fqcn = static::class;
        $baseName = basename(str_replace('\\', DIRECTORY_SEPARATOR, $fqcn));
        $hash = substr(
            Base32::encodeUnpadded(
                hash('sha256', $fqcn, true),
            ),
            0,
            4,
        );
        return '__' . $hash . '__' . $baseName;
    }

    /**
     * @param string[] $enumValues
     * @return array<string, mixed>
     */
    public static function oapiKey(
        ?string $additionalDescription = null,
        ?array $enumValues = null,
        bool $replaceDescription = false,
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

    /**
     * @param string|callable $category
     * @param array<mixed> $items
     * @param string|callable|null $keyColumn
     * @param string|string[]|null $splatnetKeys
     */
    public static function oapiKeyValueTable(
        string $valueLabel,
        /* string|callable */ $category,
        array $items,
        /* ?string|callable */ $keyColumn = 'key',
        ?string $valueColumn = 'name',
        ?string $keyLabelHtml = null,
        /* null|string|array */ $splatnetKeys = null,
    ): ?string {
        if (!$items) {
            return null;
        }

        if ($keyColumn === null) {
            $keyColumn = 'key';
        }

        if ($valueColumn === null) {
            $valueColumn = 'name';
        }

        if ($keyLabelHtml === null) {
            $keyLabelHtml = Html::tag('code', Html::encode('key'));
        }

        $splatnetKeys = $splatnetKeys ? (array)$splatnetKeys : [];

        return Html::tag('table', implode('', [
            static::oapiKeyValueTableThead($keyLabelHtml, $valueLabel, $splatnetKeys),
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
                            ArrayHelper::getValue($item, $valueColumn),
                        );
                    },
                ),
                ArrayHelper::getColumn(
                    $items,
                    fn ($item): array => array_map(
                        fn ($key): string => (string)ArrayHelper::getValue($item, $key),
                        $splatnetKeys,
                    ),
                ),
            ),
        ]));
    }

    /** @param string[] $splatnetKeys */
    private static function oapiKeyValueTableThead(
        string $keyLabelHtml,
        string $valueLabel,
        array $splatnetKeys,
    ): string {
        return Html::tag('thead', Html::tag('tr', implode('', [
            Html::tag('th', $keyLabelHtml),
            Html::tag('th', Html::encode($valueLabel)),
            $splatnetKeys
                ? Html::tag(
                    'th',
                    Html::encode(Yii::t('app-apidoc2', 'SplatNet specified ID')),
                    ['colspan' => (string)count($splatnetKeys)],
                )
                : '',
        ])));
    }

    /**
     * @param string[] $keys
     * @param string[] $values
     * @param string[] $splatnetValues
     */
    private static function oapiKeyValueTableTbody(
        array $keys,
        array $values,
        array $splatnetValues,
    ): string {
        return Html::tag('tbody', implode('', array_map(
            fn (string $key, string $value, array $splatnetValues): string => Html::tag('tr', implode('', [
                Html::tag('td', Html::tag('code', Html::encode($key))),
                Html::tag('td', Html::encode($value)),
                implode('', array_map(
                    fn (string $value): string => Html::tag(
                        'td',
                        $value === '' ? '' : Html::tag('code', Html::encode($value)),
                    ),
                    $splatnetValues,
                )),
            ])),
            $keys,
            $values,
            $splatnetValues,
        )));
    }
}

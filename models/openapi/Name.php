<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\openapi;

use Yii;
use app\models\Language;
use yii\base\Component;

use function strtr;

use const SORT_ASC;

class Name extends Component
{
    use Util;

    /**
     * @return array<string, mixed>
     */
    public static function openApiSchema(): array
    {
        $props = [];
        foreach (static::languages() as $lang) {
            $props[static::langCode($lang->lang)] = [
                'type' => 'string',
                'description' => $lang->name,
            ];
        }
        return [
            'type' => 'object',
            'description' => Yii::t('app-apidoc1', 'Internationalized name'),
            'properties' => $props,
        ];
    }

    /**
     * @return list<class-string>
     */
    public static function openApiDepends(): array
    {
        return [];
    }

    /**
     * @param array<int|string, mixed> $options
     * @return array<string, string>
     */
    public static function example(string $category, string $value, array $options = []): array
    {
        $result = [];
        foreach (static::languages() as $lang) {
            $result[static::langCode($lang->lang)] = Yii::t(
                $category,
                $value,
                $options,
                $lang->lang,
            );
        }
        return $result;
    }

    /**
     * @return list<Language>
     */
    protected static function languages(): array
    {
        static $cache = null;
        if (!$cache) {
            $cache = Language::find()
                ->standard()
                ->orderBy(['lang' => SORT_ASC])
                ->all();
        }
        return $cache;
    }

    protected static function langCode(string $code): string
    {
        return strtr($code, '-', '_');
    }
}

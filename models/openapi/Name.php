<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models\openapi;

use Yii;
use app\models\Language;
use yii\base\Component;

class Name extends Component
{
    use Util;

    public static function openApiSchema(): array
    {
        $props = [];
        foreach (static::languages() as $lang) {
            $props[$lang->lang] = [
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

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example(string $category, string $value, array $options = []): array
    {
        $result = [];
        foreach (static::languages() as $lang) {
            $result[$lang->lang] = Yii::t($category, $value, $options, $lang->lang);
        }
        return $result;
    }

    protected static function languages(): array
    {
        static $cache = null;
        if (!$cache) {
            $cache = Language::find()
                ->orderBy(['lang' => SORT_ASC])
                ->all();
        }
        return $cache;
    }
}

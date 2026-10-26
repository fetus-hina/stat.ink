<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\models\openapi;

use Yii;
use yii\base\Component;

class PermanentID extends Component
{
    use Util;

    /**
     * @return array<string, mixed>
     */
    public static function openApiSchema(): array
    {
        return [
            'type' => 'integer',
            'format' => 'int32',
            'nullable' => false,
            'minimum' => 1,
            'maximum' => 0x7fffffff,
            'description' => Yii::t('app-apidoc2', 'Permanent ID'),
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
     */
    public static function example(string $category, string $value, array $options = []): int
    {
        return 42;
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\openapi;

use Yii;
use yii\base\Component;

class PermanentID extends Component
{
    use Util;

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

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example(string $category, string $value, array $options = []): int
    {
        return 42;
    }
}

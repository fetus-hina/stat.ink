<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\openapi;

use Yii;
use yii\base\Component;

class Apikey extends Component
{
    use Util;

    public static function openApiSchema(): array
    {
        return [
            'type' => 'string',
            'pattern' => '^[0-9A-Za-z_-]{43}$',
            'description' => Yii::t('app-apidoc1', 'API Token'),
            'example' => static::example(),
        ];
    }

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example(): string
    {
        return 'fw50hytJKRe91FHuL4-K_SnzQ9Fwgwf2t_It3mQSuBU';
    }
}

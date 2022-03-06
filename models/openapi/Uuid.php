<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\openapi;

use Yii;
use yii\base\Component;

class Uuid extends Component
{
    use Util;

    public static function openApiSchema(): array
    {
        return [
            'type' => 'string',
            'format' => 'uuid',
            'pattern' => implode('-', [
                '[0-9a-f]{8}',
                '[0-9a-f]{4}',
                '[0-9a-f]{4}',
                '[0-9a-f]{4}',
                '[0-9a-f]{12}',
            ]),
            'nullable' => false,
            'description' => implode("\n", [
                Yii::t('app-apidoc2', 'Unique ID (UUID)'),
                '',
                Yii::t(
                    'app-apidoc2',
                    'Refer to [RFC 4122](https://tools.ietf.org/html/rfc4122) for format details.'
                ),
            ]),
        ];
    }

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example(string $category, string $value, array $options = []): string
    {
        return '982902e7-fde3-439f-94ef-b4e8dd93af95';
    }
}

<?php

/**
 * @copyright Copyright (C) 2019-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi;

use Yii;
use yii\base\Component;

class SplatNet2PrincipalID extends Component
{
    use Util;

    public static function openApiSchema(): array
    {
        return [
            'type' => 'string',
            'pattern' => '[0-9a-f]{16}',
            'minLength' => 16,
            'maxLength' => 16,
            'description' => Yii::t('app-apidoc2', 'SplatNet\'s `principal_id`'),
        ];
    }

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example(string $category, string $value, array $options = []): ?string
    {
        return '3f6fb10a91b0c551';
    }
}

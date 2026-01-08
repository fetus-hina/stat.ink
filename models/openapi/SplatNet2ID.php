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

class SplatNet2ID extends Component
{
    use Util;

    public static function openApiSchema(): array
    {
        return [
            'type' => 'integer',
            'format' => 'int32',
            'nullable' => true,
            'description' => Yii::t('app-apidoc2', 'SplatNet specified ID'),
        ];
    }

    public static function openApiDepends(): array
    {
        return [];
    }

    public static function example(string $category, string $value, array $options = []): ?int
    {
        return 42;
    }
}

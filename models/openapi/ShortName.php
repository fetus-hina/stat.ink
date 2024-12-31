<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\models\openapi;

use Yii;

use function array_merge;

class ShortName extends Name
{
    public static function openApiSchema(): array
    {
        return array_merge(parent::openApiSchema(), [
            'description' => Yii::t('app-apidoc1', 'Internationalized short name'),
        ]);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class Spl3BadgeAsset extends AssetBundle
{
    /**
     * @var string
     */
    public $sourcePath = '@app/resources/badge3';

    /**
     * @var array{only: string[]}
     */
    public $publishOptions = [
        'only' => [
            '*.png',
        ],
    ];
}

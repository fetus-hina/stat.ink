<?php

/**
 * @copyright Copyright (C) 2019-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;

final class FlagIconCssAsset extends AssetBundle
{
    public $sourcePath = '@vendor/lipis/flag-icons';
    public $css = [
        'css/flag-icons.min.css',
    ];
    public $publishOptions = [
        'only' => [
            'css/*',
            'flags/*/*',
        ],
    ];
}

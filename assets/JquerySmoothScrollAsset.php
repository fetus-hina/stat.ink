<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JquerySmoothScrollAsset extends AssetBundle
{
    public $sourcePath = '@node/jquery-smooth-scroll';
    public $js = [
        'jquery.smooth-scroll.min.js',
    ];
    public $depends = [
        JqueryAsset::class,
    ];
    public $publishOptions = [
        'only' => [
            '*.js',
        ],
    ];
}

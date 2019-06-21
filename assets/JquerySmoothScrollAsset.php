<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

namespace app\assets;

use yii\web\AssetBundle;
use yii\web\JqueryAsset;

class JquerySmoothScrollAsset extends AssetBundle
{
    public $sourcePath = '@npm/jquery-smooth-scroll';
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

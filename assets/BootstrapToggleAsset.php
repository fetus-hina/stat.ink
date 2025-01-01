<?php

/**
 * @copyright Copyright (C) 2016-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use yii\web\AssetBundle;

class BootstrapToggleAsset extends AssetBundle
{
    public $sourcePath = '@node/bootstrap-toggle';
    public $js = [
        'js/bootstrap-toggle.min.js',
    ];
    public $css = [
        'css/bootstrap-toggle.min.css',
    ];
    public $depends = [
        'yii\bootstrap\BootstrapAsset',
        'yii\web\JqueryAsset',
    ];
}

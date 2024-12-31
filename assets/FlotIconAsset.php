<?php

/**
 * @copyright Copyright (C) 2015-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use yii\web\AssetBundle;

class FlotIconAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/flot-graph-icon';
    public $js = [
        'jquery.flot.icon.js',
    ];
    public $depends = [
        'jp3cki\yii2\flot\FlotAsset',
    ];
}

<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class SwipeboxAsset extends AssetBundle
{
    public $sourcePath = '@bower/swipebox/src';
    public $js = [
        'js/jquery.swipebox.min.js',
    ];
    public $css = [
        'css/swipebox.min.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];
}

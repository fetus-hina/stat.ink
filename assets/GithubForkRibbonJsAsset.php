<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class GithubForkRibbonJsAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/gh-fork-ribbon';
    public $js = [
        'gh-fork-ribbon.js',
    ];
    public $css = [
        'gh-fork-ribbon.css',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'app\assets\GithubForkRibbonCssAsset',
    ];
}

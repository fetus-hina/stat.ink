<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class GithubForkRibbonJsAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/gh-fork-ribbon';
    public $js = [
        'gh-fork-ribbon.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'app\assets\GithubForkRibbonCssAsset',
        'app\assets\FontAwesomeAsset',
    ];
}

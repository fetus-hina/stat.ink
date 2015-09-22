<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class GithubForkRibbonCssAsset extends AssetBundle
{
    public $sourcePath = '@bower/github-fork-ribbon-css';
    public $css = [
        'gh-fork-ribbon.css'
    ];
}

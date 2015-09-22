<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/fest.ink';
    public $css = [
        'fest.css',
    ];
    public $js = [
        'fest.js',
    ];
    public $jsOptions = [
        'async' => 'async',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapThemeAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'app\assets\IkamodokiAsset',
        'app\assets\FlotAsset',
        'app\assets\FontAwesomeAsset',
        'app\assets\GithubForkRibbonCssAsset',
        'app\assets\GithubForkRibbonJsAsset',
        'app\assets\TwitterWidgetAsset',
    ];
}

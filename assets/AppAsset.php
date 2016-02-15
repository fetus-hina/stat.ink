<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use yii\web\AssetBundle;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'main.css',
    ];
    public $js = [
        'main.js',
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'jp3cki\yii2\jqueryColor\JqueryColorAsset',
        'rmrevin\yii\fontawesome\AssetBundle',
        'app\assets\JquerySmoothScrollAsset',
    ];

    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        $manager = $view->getAssetManager();
        $view->registerLinkTag([
            'rel' => 'icon',
            'type' => 'image/png',
            'href' => $manager->getAssetUrl($this, 'favicon.png'),
        ]);
    }
}

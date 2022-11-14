<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use Yii;
use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class AppAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/stat.ink';
    public $css = [
        'main.css',
        'font.css',
        'flot-support.css',
    ];
    public $depends = [
        AutoTooltipAsset::class,
        BabelPolyfillAsset::class,
        BootstrapAsset::class,
        BootstrapPluginAsset::class,
        ColorSchemeAsset::class,
        FluidLayoutAsset::class,
        FontAwesomeAsset::class,
        JqueryAsset::class,
        LineSeedJpAsset::class,
        LineSeedKrAsset::class,
        LinkExternalAsset::class,
        LinkPrevNextAsset::class,
        NotoSansAsset::class,
        RewriteLinkForIosAppAsset::class,
        SmoothScrollAsset::class,
        YiiAsset::class,
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

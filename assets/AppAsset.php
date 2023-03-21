<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap\BootstrapAsset;
use yii\bootstrap\BootstrapPluginAsset;
use yii\web\AssetBundle;
use yii\web\AssetManager;
use yii\web\JqueryAsset;
use yii\web\View;
use yii\web\YiiAsset;

use function gettype;
use function sprintf;
use function str_ends_with;

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

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);
        $this->registerFavicons($view);
    }

    private function registerFavicons(View $view): void
    {
        $manager = $view->getAssetManager();
        $this->registerFavicon($view, $manager, 'favicon.svg', sizes: 'any');
        $this->registerFavicon($view, $manager, 'favicon-016.png', sizes: 16);
        $this->registerFavicon($view, $manager, 'favicon-032.png', sizes: 32);
        $this->registerFavicon($view, $manager, 'favicon-057.png', sizes: 57);
        $this->registerFavicon($view, $manager, 'favicon-076.png', sizes: 76);
        $this->registerFavicon($view, $manager, 'favicon-096.png', sizes: 96);
        $this->registerFavicon($view, $manager, 'favicon-120.png', sizes: 120);
        $this->registerFavicon($view, $manager, 'favicon-128.png', sizes: 128);
        $this->registerFavicon($view, $manager, 'favicon-144.png', sizes: 144);
        $this->registerFavicon($view, $manager, 'favicon-152.png', sizes: 152);
        $this->registerFavicon($view, $manager, 'favicon-167.png', sizes: 167);
        $this->registerFavicon($view, $manager, 'favicon-180.png', sizes: 180);
        $this->registerFavicon($view, $manager, 'favicon-192.png', sizes: 192);
        $this->registerFavicon($view, $manager, 'favicon.png');
    }

    private function registerFavicon(
        View $view,
        AssetManager $am,
        string $fileName,
        int|string|null $sizes = null,
    ): void {
        $view->registerLinkTag([
            'href' => $am->getAssetUrl($this, $fileName),
            'rel' => 'icon',
            'sizes' => match (gettype($sizes)) {
                'string' => $sizes,
                'integer' => sprintf('%1$dx%1$d', $sizes),
                default => null,
            },
            'type' => match (true) {
                str_ends_with($fileName, '.png') => 'image/png',
                str_ends_with($fileName, '.svg') => 'image/svg+xml',
                default => throw new LogicException(),
            },
        ]);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
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
        LinkExternalAsset::class,
        LinkPrevNextAsset::class,
        RewriteLinkForIosAppAsset::class,
        SmoothScrollAsset::class,
        YiiAsset::class,
    ];

    public function init()
    {
        parent::init();

        if (!static::isMobileAccess()) {
            $lang = substr(Yii::$app->language, 0, 3);
            switch ($lang) {
                case 'ja-':
                    $this->depends[] = NotoSansJPAsset::class;
                    break;

                case 'zh-':
                    if (
                        Yii::$app->language === 'zh-TW' ||
                        Yii::$app->language === 'zh-HK' ||
                        strpos(Yii::$app->language, 'Hant') !== false // e.g., zh-cmn-Hant
                    ) {
                        $this->depends[] = NotoSansTCAsset::class;
                    } else {
                        $this->depends[] = NotoSansSCAsset::class;
                    }
                    break;
            }
        }
    }

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

    private function isMobileAccess(): bool
    {
        $ua = trim((string)($_SERVER['HTTP_USER_AGENT'] ?? ''));
        if ($ua === '') {
            return false;
        }

        if (
            strpos($ua, 'Android') !== false ||
            strpos($ua, 'iPhone') !== false ||
            strpos($ua, 'iPad') !== false ||
            strpos($ua, 'iPod') !== false
        ) {
            return true;
        }

        return false;
    }
}

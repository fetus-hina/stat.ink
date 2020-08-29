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
                    $this->css[] = 'font-ja.css';
                    break;

                case 'zh-':
                    $this->depends[] = NotoSansSCAsset::class;
                    $this->depends[] = NotoSansTCAsset::class;
                    if (
                        Yii::$app->language === 'zh-TW' ||
                        Yii::$app->language === 'zh-HK' ||
                        strpos(Yii::$app->language, 'Hant') !== false // e.g., zh-cmn-Hant
                    ) {
                        $this->css[] = 'font-zh-hant.css';
                    } else {
                        $this->css[] = 'font-zh-hans.css';
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

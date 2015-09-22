<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class FaviconAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/favicon';

    public function getIsActive()
    {
        return !!file_exists(Yii::getAlias($this->sourcePath . '/favicon.ico'));
    }

    public function registerAssetFiles($view)
    {
        $manager = $view->getAssetManager();
        if ($this->getIsActive()) {
            // 古き良き favicon.ico
            $view->registerLinkTag([
                'rel' => 'icon',
                'type' => 'image/vnd.microsoft.icon',
                'href' => $manager->getAssetUrl($this, 'favicon.ico'),
            ]);

            foreach ([76, 120, 152, 180] as $size) {
                // iOS 用
                $view->registerLinkTag([
                    'rel' => 'apple-touch-icon',
                    'sizes' => "{$size}x{$size}",
                    'type' => 'image/png',
                    'href' => $manager->getAssetUrl($this, "{$size}x{$size}-precomposed.png"),
                ]);

                // Android 用
                $view->registerLinkTag([
                    'rel' => 'icon',
                    'sizes' => "{$size}x{$size}",
                    'type' => 'image/png',
                    'href' => $manager->getAssetUrl($this, "{$size}x{$size}-precomposed.png"),
                ]);
            }

            // 古いiOSと一部のAndroid用に必要らしい
            $view->registerLinkTag([
                'rel' => 'apple-touch-icon-precomposed',
                'type' => 'image/png',
                'href' => $manager->getAssetUrl($this, '180x180-precomposed.png'),
            ]);
            $view->registerLinkTag([
                'rel' => 'shortcut icon',
                'type' => 'image/png',
                'href' => $manager->getAssetUrl($this, '180x180-precomposed.png'),
            ]);
        }
        return parent::registerAssetFiles($view);
    }

    public function publish($am)
    {
        $path = Yii::getAlias($this->sourcePath);
        if (!file_exists($path)) {
            mkdir($path, 0755, true);
        }
        return parent::publish($am);
    }
}

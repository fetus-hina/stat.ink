<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use Yii;
use yii\web\AssetBundle;

class AppleStartupAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/apple-startup';

    public function registerAssetFiles($view)
    {
        $manager = $view->getAssetManager();
        $files = [
            'p-768x1024@2x.png',
            'p-768x1024@1x.png',
            'p-414x736@3x.png',
            'p-375x667@2x.png',
            'p-320x568@2x.png',
            'p-320x480@2x.png',
            'p-320x480@1x.png',
            'l-768x1024@2x.png',
            'l-768x1024@1x.png',
            'l-414x736@3x.png',
            'l-375x667@2x.png',
            'l-320x568@2x.png',
            'l-320x480@2x.png',
            'l-320x480@1x.png',
        ];

        foreach ($files as $file) {
            if (!preg_match('/^([pl])-(\d+)x(\d+)@(\d+)x/', $file, $match)) {
                continue;
            }
            $media = [
                'device-width: ' . $match[2] . 'px',
                'device-height: ' . $match[3] . 'px',
                '-webkit-device-pixel-ratio: ' . $match[4],
                'orientation: ' . ($match[1] === 'l' ? 'landscape' : 'portrait'),
            ];
            $view->registerLinkTag([
                'rel' => 'apple-touch-startup-image',
                'type' => 'image/png',
                'media' => '(' . implode(') and (', $media) . ')',
                'href' => $manager->getAssetUrl($this, $file),
            ]);
        }
        return parent::registerAssetFiles($view);
    }
}

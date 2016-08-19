<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\assets;

use Yii;
use yii\helpers\Html;
use yii\web\AssetBundle;

class AppLinkAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/app-link-logos';

    public function getIkaLog() : string
    {
        return $this->image('ikalog.png');
    }

    public function getIkaDenwa() : string
    {
        return $this->image('ikadenwa.png');
    }

    public function getIkaNakama() : string
    {
        return $this->image('ikanakama.png');
    }

    public function getFestInk() : string
    {
        return $this->image('festink.png');
    }

    public function getSplatNet() : string
    {
        return $this->image('splatnet.png');
    }

    public function getIkaRecJa() : string
    {
        return $this->image('ikarec-ja.png');
    }

    public function getIkaRecEn() : string
    {
        return $this->image('ikarec-en.png');
    }

    protected function image(string $file) : string
    {
        return Html::img(
            Yii::$app->assetManager->getAssetUrl($this, $file),
            [
                'style' => [
                    'height' => '1em',
                    'width' => 'auto',
                ],
            ]
        );
    }
}

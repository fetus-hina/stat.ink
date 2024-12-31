<?php

/**
 * @copyright Copyright (C) 2016-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\assets;

use Yii;
use yii\helpers\Html;
use yii\web\AssetBundle;

class AppLinkAsset extends AssetBundle
{
    public $sourcePath = '@app/resources/.compiled/app-link-logos';
    public $depends = [
        FontAwesomeAsset::class,
    ];

    public function getIkaLog(): string
    {
        return $this->image('ikalog.png');
    }

    public function getIkaDenwa(): string
    {
        return $this->image('ikadenwa.png');
    }

    public function getIkaNakama(): string
    {
        return $this->image('ikanakama.png');
    }

    public function getFestInk(): string
    {
        return $this->image('festink.png');
    }

    public function getSplatNet(): string
    {
        return $this->image('splatnet.png');
    }

    public function getIkaRecJa(): string
    {
        return $this->image('ikarec-ja.png');
    }

    public function getIkaRecEn(): string
    {
        return $this->image('ikarec-en.png');
    }

    public function getSquidTracks(): string
    {
        return $this->image('squidtracks.png');
    }

    public function getSwitch(): string
    {
        return $this->image('switch.min.svg');
    }

    public function getInkipedia(): string
    {
        return $this->image('inkipedia.png');
    }

    protected function image(string $file): string
    {
        return Html::tag(
            'span',
            Html::img(
                Yii::$app->assetManager->getAssetUrl($this, $file),
                [
                    'style' => [
                        'height' => '1em',
                        'width' => 'auto',
                    ],
                ],
            ),
            ['class' => 'fas fa-fw'],
        );
    }
}

<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\IrasutoyaAsset;
use statink\yii2\paintball\PaintballAsset;
use yii\base\Widget;
use yii\helpers\Html;

class Reiwa extends Widget
{
    public function run()
    {
        $time = time();

        if ($time < strtotime('2019-05-01T00:00:00+09:00')) {
            return '';
        }

        if ($time >= strtotime('2019-05-08T00:00:00+09:00')) {
            return '';
        }

        return $this->renderWidget();
    }

    protected function renderWidget(): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderIcon(),
                Html::tag('div', $this->renderText(), ['style' => 'margin-left:15px']),
            ]),
            [
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'margin-bottom' => '20px',
                ],
            ]
        );
    }

    protected function renderIcon(): string
    {
        $asset = IrasutoyaAsset::register($this->view);
        $am = Yii::$app->assetManager;

        $img = Html::img(
            $am->getAssetUrl(
                $asset,
                'reiwa.png'
            ),
            ['style' => [
                'width' => 'auto',
                'height' => '100px',
            ]]
        );

        $lang = Yii::$app->language;
        switch (substr($lang, 0, 2)) {
            case 'ja':
                return Html::a($img, 'https://ja.wikipedia.org/wiki/%E4%BB%A4%E5%92%8C');

            default:
                return Html::a($img, 'https://en.wikipedia.org/wiki/Reiwa');
        }
    }

    protected function renderText(): string
    {
        $lang = Yii::$app->language;
        switch (substr($lang, 0, 2)) {
            case 'ja':
                return $this->renderTextJa();

            default:
                return $this->renderTextEn();
        }
    }

    protected function renderTextJa(): string
    {
        PaintballAsset::register($this->view);
        return Html::tag(
            'p',
            Html::encode('Happy New Era!'),
            [
                'class' => 'paintball',
                'style' => [
                    'font-size' => '42px',
                ],
            ]
        );
    }

    protected function renderTextEn(): string
    {
        $fmt = Yii::$app->formatter;
        return implode('', [
            $this->renderTextJa(),
            Html::tag('p', sprintf(
                'In Japan, on %s, the new emperor "%s" was crowned, and a new (%s) era "%s" began.',
                $fmt->asDate('2019-05-01', 'long'),
                Html::a(
                    "天皇 (Ten'nō)",
                    'https://en.wikipedia.org/wiki/Emperor_of_Japan'
                ),
                $fmt->asOrdinal(248),
                Html::a(
                    '令和 (Reiwa)',
                    'https://en.wikipedia.org/wiki/Reiwa'
                )
            )),
            Html::tag('p', 'Welcome the new emperor and deeply thank the former emperor.'),
        ]);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\assets\IrasutoyaAsset;
use app\assets\PaintballAsset;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;
use function substr;

final class HappyNewYearWidget extends Widget
{
    public function run()
    {
        $now = new DateTimeImmutable(
            'now',
            new DateTimeZone(Yii::$app->timeZone),
        );

        $month = (int)$now->format('n');
        if ($month !== 1) {
            return '';
        }
        $day = (int)$now->format('j');
        if ($day > 5) {
            return '';
        }
        $year = (int)$now->format('Y');
        return $this->renderWidget(($year + 8) % 12);
    }

    protected function renderWidget(int $eto): string
    {
        return Html::tag(
            'div',
            implode('', [
                $this->renderIcon($eto),
                $this->renderText(),
            ]),
            [
                'style' => [
                    'display' => 'flex',
                    'align-items' => 'center',
                    'margin-bottom' => '15px',
                ],
            ],
        );
    }

    protected function renderIcon(int $eto): string
    {
        PaintballAsset::register($this->view);
        $asset = IrasutoyaAsset::register($this->view);
        $am = Yii::$app->assetManager;

        $img = Html::img(
            $am->getAssetUrl(
                $asset,
                "eto/{$eto}.png",
            ),
            [
                'style' => [
                    'width' => 'auto',
                    'height' => '100px',
                ],
            ],
        );

        $lang = Yii::$app->language;
        switch (substr($lang, 0, 2)) {
            case 'ja':
            case 'ko':
            case 'zh':
                return $img;

            default:
                return Html::a($img, 'https://en.wikipedia.org/wiki/Sexagenary_cycle');
        }
    }

    protected function renderText(): string
    {
        return Html::tag(
            'p',
            Html::encode('Happy New Year!'),
            [
                'class' => 'paintball',
                'style' => [
                    'margin-left' => '15px',
                    'font-size' => '42px',
                ],
            ],
        );
    }
}

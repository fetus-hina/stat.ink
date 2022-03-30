<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Yii;
use app\assets\CookieAlertAsset;
use app\components\helpers\Html;
use yii\base\Widget;

class CookieAlert extends Widget
{
    public function run(): string
    {
        $isBot = $this->isBotAccess();
        if ($isBot === null || $isBot === true) {
            return '';
        }

        CookieAlertAsset::register($this->view);

        return Html::tag(
            'div',
            implode(' ', [
                Html::encode(Yii::t(
                    'app-cookie',
                    'We use cookies to ensure you get the best experience on our website.'
                )),
                Html::a(
                    Html::encode(Yii::t('app-cookie', 'Privacy policy')),
                    ['site/privacy'],
                    [
                        'class' => 'alert-link',
                        'target' => '_blank',
                    ]
                ),
                Html::button(
                    Html::encode(Yii::t('app-cookie', 'I agree')),
                    [
                        'class' => 'btn btn-primary btn-sm acceptcookies',
                        'aria-label' => Yii::t('app', 'Close'),
                    ]
                ),
            ]),
            [
                'class' => 'alert text-center cookiealert',
                'role' => 'alert',
            ]
        );
    }

    private function isBotAccess(): ?bool
    {
        $ua = trim((string)Yii::$app->request->userAgent);
        $crawlerDetect = new CrawlerDetect();
        if ($ua === '') {
            return null;
        }

        return (bool)$crawlerDetect->isCrawler($ua);
    }
}

<?php

/**
 * @copyright Copyright (C) 2019-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Yii;
use app\assets\CookieAlertAsset;
use yii\base\Widget;
use yii\helpers\Html;

use function implode;
use function trim;

class CookieAlert extends Widget
{
    public function run(): string
    {
        Yii::beginProfile(__METHOD__, __METHOD__);
        try {
            $isBot = $this->isBotAccess();
            if ($isBot === null || $isBot === true) {
                return '';
            }

            CookieAlertAsset::register($this->view);

            return Yii::$app->cache->getOrSet(
                [__METHOD__, Yii::$app->language],
                fn (): string => Html::tag(
                    'div',
                    implode(' ', [
                        Html::encode(
                            Yii::t(
                                'app-cookie',
                                'We use cookies to ensure you get the best experience on our website.',
                            ),
                        ),
                        Html::a(
                            Html::encode(Yii::t('app-cookie', 'Privacy policy')),
                            ['site/privacy'],
                            [
                                'class' => 'alert-link',
                                'target' => '_blank',
                            ],
                        ),
                        Html::button(
                            Html::encode(Yii::t('app-cookie', 'I agree')),
                            [
                                'class' => 'btn btn-primary btn-sm acceptcookies',
                                'aria-label' => Yii::t('app', 'Close'),
                            ],
                        ),
                    ]),
                    [
                        'class' => 'alert text-center cookiealert',
                        'role' => 'alert',
                    ],
                ),
                7200,
            );
        } finally {
            Yii::endProfile(__METHOD__, __METHOD__);
        }
    }

    private function isBotAccess(): ?bool
    {
        $method = __METHOD__;
        $ua = trim((string)Yii::$app->request->userAgent);
        return Yii::$app->cache->getOrSet(
            [$method, $ua],
            function () use ($method, $ua): ?bool {
                Yii::beginProfile($ua, $method);
                try {
                    $crawlerDetect = new CrawlerDetect();
                    if ($ua === '') {
                        return null;
                    }

                    return (bool)$crawlerDetect->isCrawler($ua);
                } finally {
                    Yii::endProfile($ua, $method);
                }
            },
            86400,
        );
    }
}

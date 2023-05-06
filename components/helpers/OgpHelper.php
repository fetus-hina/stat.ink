<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\models\User;
use yii\web\View;

final class OgpHelper
{
    public static function profileV3(View $view, User $user, string $url): void
    {
        if (Yii::$app->params['useS3ImgGen']) {
            self::profileV3Image($view, $user, $url);
        } else {
            self::profileV3Summary($view, $user, $url);
        }
    }

    private static function profileV3Summary(View $view, User $user, string $url): void
    {
        $title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
        $data = [
            'og:description' => $title,
            'og:image' => Url::to($user->iconUrl, true),
            'og:site_name' => Yii::$app->name,
            'og:title' => $title,
            'og:type' => 'website',
            'og:url' => $url,
            'twitter:card' => 'summary',
            'twitter:description' => $title,
            'twitter:image' => Url::to($user->iconUrl, true),
            'twitter:title' => $title,
            'twitter:url' => $url,
        ];

        foreach ($data as $k => $v) {
            $view->registerMetaTag(['name' => $k, 'content' => $v]);
        }
    }

    private static function profileV3Image(View $view, User $user, string $url): void
    {
        $title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
        $data = [
            'og:type' => 'website',
            'og:description' => $title,
            'og:image' => vsprintf('https://s3-img-gen.stats.ink/ogp/profile/en-US/%s.jpg', [
                rawurlencode($user->screen_name),
            ]),
            'og:site_name' => Yii::$app->name,
            'og:title' => $title,
            'og:url' => $url,
            'twitter:card' => 'summary_large_image',
            'twitter:description' => $title,
            'twitter:image' => vsprintf('https://s3-img-gen.stats.ink/ogp/profile/en-US/%s.jpg', [
                rawurlencode($user->screen_name),
            ]),
            'twitter:title' => $title,
            'twitter:url' => $url,
        ];

        foreach ($data as $k => $v) {
            $view->registerMetaTag(['name' => $k, 'content' => $v]);
        }
    }
}

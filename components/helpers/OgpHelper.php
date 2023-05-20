<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Throwable;
use Yii;
use app\models\User;
use yii\helpers\Url;
use yii\web\View;

use function rawurlencode;
use function vsprintf;

final class OgpHelper
{
    private const NAME_WITH_SLOGAN = 'stat.ink -- Go Deeper, Have More Fun.';
    private const DEFAULT_DESCRIPTION = 'stat.ink saves and analyzes your Splatoon results.';

    public static function default(
        View $view,
        ?string $url = null,
        string $title = self::NAME_WITH_SLOGAN,
        ?string $description = null,
    ): void {
        if ($url === null) {
            try {
                $url = Url::current();
            } catch (Throwable $e) {
                $url = Url::to(['site/index']);
            }
        }
        $url = Url::to($url, true);

        if ($description === null) {
            $description = $title === self::NAME_WITH_SLOGAN
                ? self::DEFAULT_DESCRIPTION
                : self::NAME_WITH_SLOGAN;
        }

        $data = [
            'og:description' => $description,
            'og:image' => 'https://s3-img-gen.stats.ink/ogp/default.jpg',
            'og:image:alt' => self::NAME_WITH_SLOGAN,
            'og:site_name' => Yii::$app->name,
            'og:title' => $title,
            'og:type' => 'website',
            'og:url' => $url,
            'twitter:card' => 'summary_large_image',
            'twitter:description' => $description,
            'twitter:image' => 'https://s3-img-gen.stats.ink/ogp/default.jpg',
            'twitter:title' => $title,
            'twitter:url' => $url,
        ];

        foreach ($data as $k => $v) {
            $view->registerMetaTag(['name' => $k, 'content' => $v]);
        }
    }

    public static function profileV3(
        View $view,
        User $user,
        ?string $url = null,
        ?string $description = self::NAME_WITH_SLOGAN,
    ): void {
        if ($url === null) {
            try {
                $url = Url::current();
            } catch (Throwable $e) {
                $url = Url::to(['site/index']);
            }
        }
        $url = Url::to($url, true);

        $title = Yii::t('app', "{name}'s Splat Log", ['name' => $user->name]);
        if ($description === null) {
            $description = self::NAME_WITH_SLOGAN;
        }

        if (Yii::$app->params['useS3ImgGen']) {
            self::profileV3Image(
                view: $view,
                user: $user,
                url: $url,
                title: $title,
                description: $description,
            );
        } else {
            self::profileV3Summary(
                view: $view,
                user: $user,
                url: $url,
                title: $title,
                description: $description,
            );
        }
    }

    private static function profileV3Summary(
        View $view,
        User $user,
        string $url,
        string $title,
        string $description,
    ): void {
        $data = [
            'og:description' => $description,
            'og:image' => Url::to($user->iconUrl, true),
            'og:image:alt' => $title,
            'og:site_name' => Yii::$app->name,
            'og:title' => $title,
            'og:type' => 'website',
            'og:url' => $url,
            'twitter:card' => 'summary',
            'twitter:description' => $description,
            'twitter:image' => Url::to($user->iconUrl, true),
            'twitter:title' => $title,
            'twitter:url' => $url,
        ];

        foreach ($data as $k => $v) {
            $view->registerMetaTag(['name' => $k, 'content' => $v]);
        }
    }

    private static function profileV3Image(
        View $view,
        User $user,
        string $url,
        string $title,
        string $description,
    ): void {
        $imageUrl = vsprintf('https://s3-img-gen.stats.ink/ogp/profile/en-US/%s.jpg', [
            rawurlencode($user->screen_name),
        ]);
        $data = [
            'og:description' => $description,
            'og:image' => $imageUrl,
            'og:image:alt' => $title,
            'og:site_name' => Yii::$app->name,
            'og:title' => $title,
            'og:type' => 'website',
            'og:url' => $url,
            'twitter:card' => 'summary_large_image',
            'twitter:description' => $description,
            'twitter:image' => $imageUrl,
            'twitter:title' => $title,
            'twitter:url' => $url,
        ];

        foreach ($data as $k => $v) {
            $view->registerMetaTag(['name' => $k, 'content' => $v]);
        }
    }
}

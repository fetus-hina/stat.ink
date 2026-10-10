<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\widgets;

use Throwable;
use Uri\WhatWg\Url as WhatWgUrl;
use Yii;
use yii\base\Widget;

use function parse_str;
use function preg_match;
use function substr;
use function trim;

class EmbedVideo extends Widget
{
    public ?string $url;

    public static function isSupported(string $url): bool
    {
        return !!static::factory($url);
    }

    public function run()
    {
        $instance = static::factory($this->url);
        return $instance ? $instance->run() : '';
    }

    protected static function factory(?string $url): ?Widget
    {
        if ($url === null || $url === '') {
            return null;
        }

        try {
            $parsedUrl = WhatWgUrl::parse($url);
            if (
                !$parsedUrl ||
                ($parsedUrl->getScheme() !== 'http' && $parsedUrl->getScheme() !== 'https')
            ) {
                return null;
            }
            $host = (string)$parsedUrl->getAsciiHost();
            $path = $parsedUrl->getPath();
            $query = [];
            $queryStr = trim((string)$parsedUrl->getQuery());
            if ($queryStr !== '') {
                parse_str($queryStr, $query);
            }
            if ($host === 'www.youtube.com' && $path === '/watch') {
                if (isset($query['v']) && static::isValidYoutubeId($query['v'])) {
                    return Yii::createObject([
                        'class' => embedVideo\Youtube::class,
                        'videoId' => $query['v'],
                        'timeCode' => $query['t'] ?? null,
                    ]);
                }
            } elseif ($host === 'youtu.be' && static::isValidYoutubeId(substr($path, 1))) {
                return Yii::createObject([
                    'class' => embedVideo\Youtube::class,
                    'videoId' => substr($path, 1),
                    'timeCode' => $query['t'] ?? null,
                ]);
            } elseif ($host === 'www.twitch.tv' || $host === 'secure.twitch.tv') {
                if (preg_match('#/videos/(\d+)#', $path, $match)) {
                    return Yii::createObject([
                        'class' => embedVideo\Twitch::class,
                        'videoId' => $match[1],
                    ]);
                }
            } elseif ($host === 'www.nicovideo.jp') {
                if (preg_match('#/watch/([a-z]{2}\d+)#', $path, $match)) {
                    return Yii::createObject([
                        'class' => embedVideo\Nicovideo::class,
                        'videoId' => $match[1],
                    ]);
                }
            }
        } catch (Throwable $e) {
        }
        return null;
    }

    protected static function isValidYoutubeId(string $id): bool
    {
        return !!preg_match('/^[A-Za-z0-9_-]+$/', $id);
    }
}

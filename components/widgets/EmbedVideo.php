<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets;

use Yii;
use Zend\Uri\UriFactory;
use Zend\Uri\Http as HttpUri;
use yii\base\Widget;

class EmbedVideo extends Widget
{
    public $url;

    public static function isSupported($url)
    {
        return !!static::factory($url);
    }

    public function run()
    {
        $instance = static::factory($this->url);
        return $instance ? $instance->run() : '';
    }

    protected static function factory($url)
    {
        if ($url == '') {
            return null;
        }
        try {
            $uri = UriFactory::factory($url);
            if (!$uri->isValid() || !$uri instanceof HttpUri) {
                return null;
            }
            $host = strtolower($uri->getHost());
            $path = $uri->getPath();
            if ($host === 'www.youtube.com' && $path === '/watch') {
                $query = $uri->getQueryAsArray();
                if (isset($query['v']) && static::isValidYoutubeId($query['v'])) {
                    return Yii::createObject([
                        'class' => embedVideo\Youtube::class,
                        'videoId' => $query['v'],
                        'timeCode' => $query['t'] ?? null,
                    ]);
                }
            } elseif ($host === 'youtu.be' && static::isValidYoutubeId(substr($path, 1))) {
                $query = $uri->getQueryAsArray();
                return Yii::createObject([
                    'class' => embedVideo\Youtube::class,
                    'videoId' => substr($path, 1),
                    'timeCode' => $query['t'] ?? null,
                ]);
            } elseif ($host === 'www.twitch.tv' || $host === 'secure.twitch.tv') {
                if (preg_match('#/v/(\d+)#', $uri->getPath(), $match)) {
                    return Yii::createObject([
                        'class' => embedVideo\Twitch::class,
                        'videoId' => $match[1],
                    ]);
                }
            }
            // ニコニコ動画さんTLS対応まだー？
        } catch (\Exception $e) {
        }
        return null;
    }

    protected static function isValidYoutubeId($id)
    {
        return !!preg_match('/^[A-Za-z0-9_-]+$/', $id);
    }
}

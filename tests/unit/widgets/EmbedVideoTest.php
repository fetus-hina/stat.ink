<?php

declare(strict_types=1);

namespace tests\widgets;

use Codeception\Test\Unit;
use app\components\widgets\EmbedVideo;
use app\components\widgets\embedVideo\Nicovideo;
use app\components\widgets\embedVideo\Twitch;
use app\components\widgets\embedVideo\Youtube;
use yii\base\Widget;

final class EmbedVideoTest extends Unit
{
    /**
     * @dataProvider provideSupportedUrls
     */
    public function testFactory(
        string $url,
        string $expectedClass,
        string $expectedVideoId,
        ?string $expectedTimeCode,
    ): void {
        $widget = $this->factory($url);
        $this->assertInstanceOf($expectedClass, $widget);
        $this->assertSame($expectedVideoId, $widget->videoId);
        if ($widget instanceof Youtube) {
            $this->assertSame($expectedTimeCode, $widget->timeCode);
        }
        $this->assertTrue(EmbedVideo::isSupported($url));
    }

    public function provideSupportedUrls(): array
    {
        return [
            'YouTube watch' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                Youtube::class,
                'dQw4w9WgXcQ',
                null,
            ],
            'YouTube watch with time code' => [
                'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=42',
                Youtube::class,
                'dQw4w9WgXcQ',
                '42',
            ],
            'YouTube short URL with time code' => [
                'https://youtu.be/dQw4w9WgXcQ?t=42',
                Youtube::class,
                'dQw4w9WgXcQ',
                '42',
            ],
            'YouTube over plain HTTP' => [
                'http://www.youtube.com/watch?v=dQw4w9WgXcQ',
                Youtube::class,
                'dQw4w9WgXcQ',
                null,
            ],
            'uppercase host' => [
                'https://WWW.YOUTUBE.COM/watch?v=dQw4w9WgXcQ',
                Youtube::class,
                'dQw4w9WgXcQ',
                null,
            ],
            'uppercase scheme' => [
                'HTTPS://www.youtube.com/watch?v=dQw4w9WgXcQ',
                Youtube::class,
                'dQw4w9WgXcQ',
                null,
            ],
            'Twitch' => [
                'https://www.twitch.tv/videos/123456789',
                Twitch::class,
                '123456789',
                null,
            ],
            'Twitch (secure)' => [
                'https://secure.twitch.tv/videos/123456789',
                Twitch::class,
                '123456789',
                null,
            ],
            'niconico' => [
                'https://www.nicovideo.jp/watch/sm9',
                Nicovideo::class,
                'sm9',
                null,
            ],
        ];
    }

    /**
     * @dataProvider provideUnsupportedUrls
     */
    public function testUnsupported(?string $url): void
    {
        $this->assertNull($this->factory($url));
        if ($url !== null) {
            $this->assertFalse(EmbedVideo::isSupported($url));
        }
    }

    public function provideUnsupportedUrls(): array
    {
        return [
            'null' => [null],
            'empty' => [''],
            'not a URL' => ['not a url'],
            'scheme only' => ['https://'],
            'without scheme' => ['www.youtube.com/watch?v=dQw4w9WgXcQ'],
            'non-HTTP scheme' => ['ftp://www.youtube.com/watch?v=dQw4w9WgXcQ'],
            'unknown host' => ['https://example.com/watch?v=dQw4w9WgXcQ'],
            'YouTube watch without video ID' => ['https://www.youtube.com/watch'],
            'YouTube invalid video ID' => ['https://www.youtube.com/watch?v=%3Cscript%3E'],
            'YouTube short URL without video ID' => ['https://youtu.be/'],
            'Twitch non-video page' => ['https://www.twitch.tv/someone'],
            'niconico non-video page' => ['https://www.nicovideo.jp/ranking'],
        ];
    }

    private function factory(?string $url): ?Widget
    {
        $class = new class extends EmbedVideo {
            public static function create(?string $url): ?Widget
            {
                return static::factory($url);
            }
        };

        return $class::create($url);
    }
}

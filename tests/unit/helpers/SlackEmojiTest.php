<?php

declare(strict_types=1);

namespace tests\helpers;

use Codeception\Test\Unit;
use app\components\helpers\SlackEmoji;

final class SlackEmojiTest extends Unit
{
    public function testBuildMap(): void
    {
        $map = SlackEmoji::buildMap(
            [
                '1F604' => 'smile',
                '1F44D' => ['+1', 'thumbsup'],
                '1F44D-1F3FB' => '+1::skin-tone-2',
                '2600' => 'sunny',
                '1F1EF-1F1F5' => 'flag-jp',
                '1FFFF' => 'not-in-emoji-data',
            ],
            [
                ['hexcode' => '1F604', 'unicode' => "\u{1F604}"],
                [
                    'hexcode' => '1F44D',
                    'unicode' => "\u{1F44D}",
                    'skins' => [
                        ['hexcode' => '1F44D-1F3FB', 'unicode' => "\u{1F44D}\u{1F3FB}"],
                    ],
                ],
                ['hexcode' => '2600', 'unicode' => "\u{2600}\u{FE0F}"],
                ['hexcode' => '1F1EF-1F1F5', 'unicode' => "\u{1F1EF}\u{1F1F5}"],
            ],
        );

        $this->assertSame(
            [
                'smile' => "\u{1F604}",
                '+1' => "\u{1F44D}",
                'thumbsup' => "\u{1F44D}",
                '+1::skin-tone-2' => "\u{1F44D}\u{1F3FB}",
                'sunny' => "\u{2600}\u{FE0F}",
                'flag-jp' => "\u{1F1EF}\u{1F1F5}",
                'not-in-emoji-data' => "\u{1FFFF}",
            ],
            $map,
        );
    }

    /**
     * @dataProvider provideShortcodes
     */
    public function testFromShortcode(string $shortcode, ?string $expected): void
    {
        $this->assertSame($expected, SlackEmoji::fromShortcode($shortcode));
    }

    public function provideShortcodes(): array
    {
        return [
            'simple' => [':smile:', "\u{1F604}"],
            'alias 1' => [':+1:', "\u{1F44D}"],
            'alias 2' => [':thumbsup:', "\u{1F44D}"],
            'squid' => [':squid:', "\u{1F991}"],
            'emoji presentation selector' => [':sunny:', "\u{2600}\u{FE0F}"],
            'multiple codepoints' => [':flag-jp:', "\u{1F1EF}\u{1F1F5}"],
            'unknown' => [':no-such-emoji:', null],
            'without colons' => ['smile', null],
            'empty' => ['', null],
        ];
    }
}

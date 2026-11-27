<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use RuntimeException;
use Yii;
use yii\helpers\Json;

use function array_map;
use function explode;
use function file_get_contents;
use function hexdec;
use function implode;
use function is_array;
use function is_string;
use function mb_chr;
use function preg_match;

/**
 * Converts the Slack emoji shortcodes (e.g. ":smile:") to emoji characters
 */
final class SlackEmoji
{
    /**
     * The shortcodes of iamcal/emoji-data, which Slack uses
     */
    private const SHORTCODES_PATH = '@app/node_modules/emojibase-data/en/shortcodes/iamcal.json';

    /**
     * The emoji data. Unlike the hexcodes, its "unicode" keeps U+FE0F (emoji presentation
     * selector), which twemoji needs to recognize e.g. U+2600 as an emoji.
     */
    private const EMOJI_PATH = '@app/node_modules/emojibase-data/en/compact.json';

    /**
     * @var array<string, string>|null
     */
    private static ?array $map = null;

    public static function fromShortcode(string $shortcode): ?string
    {
        if (!preg_match('/^:([a-zA-Z0-9+._-]+):$/', $shortcode, $match)) {
            return null;
        }

        return self::getMap()[$match[1]] ?? null;
    }

    /**
     * Builds the map from shortcode to emoji, from the emojibase shortcodes data
     * (hexcode => shortcode or list of shortcodes) and the emojibase emoji data
     * (list of {hexcode, unicode, skins?})
     *
     * @param array<array-key, mixed> $shortcodesData
     * @param array<array-key, mixed> $emojiData
     * @return array<string, string>
     */
    public static function buildMap(array $shortcodesData, array $emojiData): array
    {
        $unicodes = self::buildUnicodeMap($emojiData);

        $map = [];
        foreach ($shortcodesData as $hexcode => $shortcodes) {
            $hexcode = (string)$hexcode;
            $emoji = $unicodes[$hexcode] ?? implode(
                '',
                array_map(
                    fn (string $hex): string => mb_chr((int)hexdec($hex), 'UTF-8'),
                    explode('-', $hexcode),
                ),
            );

            foreach (is_array($shortcodes) ? $shortcodes : [$shortcodes] as $shortcode) {
                if (is_string($shortcode)) {
                    $map[$shortcode] = $emoji;
                }
            }
        }

        return $map;
    }

    /**
     * @param array<array-key, mixed> $emojiData
     * @return array<string, string> hexcode => emoji
     */
    private static function buildUnicodeMap(array $emojiData): array
    {
        $map = [];
        foreach ($emojiData as $item) {
            if (!is_array($item)) {
                continue;
            }

            $variants = [$item, ...(is_array($item['skins'] ?? null) ? $item['skins'] : [])];
            foreach ($variants as $variant) {
                if (
                    is_array($variant) &&
                    is_string($variant['hexcode'] ?? null) &&
                    is_string($variant['unicode'] ?? null)
                ) {
                    $map[$variant['hexcode']] = $variant['unicode'];
                }
            }
        }

        return $map;
    }

    /**
     * @return array<string, string>
     */
    private static function getMap(): array
    {
        if (self::$map === null) {
            self::$map = self::buildMap(
                self::loadJson(self::SHORTCODES_PATH),
                self::loadJson(self::EMOJI_PATH),
            );
        }

        return self::$map;
    }

    /**
     * @return array<array-key, mixed>
     */
    private static function loadJson(string $alias): array
    {
        $path = TypeHelper::string(Yii::getAlias($alias));
        $json = file_get_contents($path);
        if ($json === false) {
            throw new RuntimeException('Failed to read ' . $path);
        }

        $data = Json::decode($json);
        if (!is_array($data)) {
            throw new RuntimeException('Invalid data: ' . $path);
        }

        return $data;
    }
}

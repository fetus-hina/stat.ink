<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace yii\helpers;

use Yii;
use app\models\Rank2;

class Html extends BaseHtml
{
    public static $enableServerPush = true;
    private static $pushed = [];

    public static function linkInkipedia(
        string $text,
        string $page,
        array $options = []
    ): string {
        return static::a(
            $text,
            sprintf(
                'https://splatoonwiki.org/wiki/%s',
                str_replace(
                    ['%20', '%3a'],
                    ['_', ':'],
                    rawurlencode($page)
                )
            ),
            $options
        );
    }

    public static function cssFile($url, $options = [])
    {
        if (static::$enableServerPush && !isset($options['condition'])) {
            $resp = Yii::$app->response;
            if ($resp && !$resp->isSent) {
                $headers = $resp->getHeaders();
                $href = Url::to($url);
                if (strpos($href, '//') === false && !in_array($href, static::$pushed, true)) {
                    static::$pushed[] = $href;
                    if ($headers->has('Link')) {
                        $headers->add('Link', sprintf(
                            '<%s>; rel=preload; as=style',
                            $href
                        ));
                    } else {
                        $headers->set('Link', sprintf(
                            '<%s>; rel=preload; as=style',
                            $href
                        ));
                    }
                }
            }
        }
        return BaseHtml::cssFile($url, $options);
    }

    public static function jsFile($url, $options = [])
    {
        if (static::$enableServerPush && !isset($options['condition'])) {
            $resp = Yii::$app->response;
            if ($resp && !$resp->isSent) {
                $headers = $resp->getHeaders();
                $href = Url::to($url);
                if (strpos($href, '//') === false && !in_array($href, static::$pushed, true)) {
                    static::$pushed[] = $href;
                    if ($headers->has('Link')) {
                        $headers->add('Link', sprintf(
                            '<%s>; rel=preload; as=script',
                            $href
                        ));
                    } else {
                        $headers->set('Link', sprintf(
                            '<%s>; rel=preload; as=script',
                            $href
                        ));
                    }
                }
            }
        }
        return BaseHtml::jsFile($url, $options);
    }

    public static function cssStyleFromArray(array $style)
    {
        $result = implode(';', array_filter(array_map(
            function (string $name, string $value): ?string {
                $name = trim($name);
                $value = trim($value);
                if ($name === '' || $value === '') {
                    return null;
                }
                return "{$name}:{$value}";
            },
            array_keys($style),
            array_values($style)
        )));
        return $result === '' ? null : rtrim($result);
    }

    public static function renderCss(array $styles): string
    {
        return implode('', array_map(
            fn (string $selector, array $style): string => sprintf('%s{%s}', $selector, static::cssStyleFromArray($style)),
            array_keys($styles),
            array_values($styles)
        ));
    }

    public static function rank2(int $rankNumber): ?string
    {
        $rankInfo = Rank2::parseRankNumber($rankNumber);
        if (!$rankInfo) {
            return null;
        }

        if ($rankInfo[1] === null) {
            return self::encode(Yii::t('app-rank2', $rankInfo[0]));
        }

        return implode('', [
            self::encode(Yii::t('app-rank2', $rankInfo[0])),
            self::tag('small', self::encode(' ' . (string)$rankInfo[1])),
        ]);
    }
}

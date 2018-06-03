<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace yii\helpers;

use Yii;

class Html extends BaseHtml
{
    public static $enableServerPush = true;
    private static $pushed = [];

    public static function linkInkipedia(
        string $text,
        string $page,
        array $options = []
    ) : string {
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
}

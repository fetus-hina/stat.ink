<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\embedVideo;

use yii\base\Widget;
use yii\helpers\Html;

use function filter_var;
use function http_build_query;
use function implode;
use function preg_match;
use function rawurlencode;
use function sprintf;

use const FILTER_VALIDATE_INT;

class Youtube extends Widget
{
    public static $autoIdPrefix = 'w-youtube-';

    public $videoId;
    public $timeCode;

    public $width = 853;
    public $height = 480;
    public $noCookie = true;
    public $rel = false;

    public function run()
    {
        $params = [];
        if (!$this->rel) {
            $params['rel'] = '0';
        }
        if ($this->timeCode) {
            if (filter_var($this->timeCode, FILTER_VALIDATE_INT)) {
                $params['start'] = (string)(int)$this->timeCode;
            } elseif (preg_match('/^(?:(\d+)h)?(?:(\d+)m)?(?:(\d+)s)?$/', $this->timeCode, $match)) {
                $fConv = function ($index) use ($match) {
                    if (!isset($match[$index])) {
                        return 0;
                    }
                    return (int)($match[$index] ?: 0);
                };
                $params['start'] = (string)($fConv(1) * 3600 + $fConv(2) * 60 + $fConv(3));
            }
        }
        $iframe = Html::tag('iframe', '', [
            'width' => (string)$this->width,
            'height' => (string)$this->height,
            'frameborder' => '0',
            'allowfullscreen' => 'allowfullscreen',
            'src' => sprintf(
                'https://%s/embed/%s?%s',
                $this->noCookie ? 'www.youtube-nocookie.com' : 'www.youtube.com',
                rawurlencode($this->videoId),
                http_build_query($params, '', '&'),
            ),
        ]);

        $this->view->registerCss(implode('', [
            sprintf('#%s{%s}', $this->getId(), Html::cssStyleFromArray([
                'position' => 'relative',
                'padding-bottom' => sprintf('%f%%', 100 * $this->height / $this->width),
                'padding-top' => '30px',
                'height' => '0',
                'overflow' => 'hidden',
            ])),
            sprintf('#%1$s iframe,#%1$s object,#%1$s embed{%2$s}', $this->getId(), Html::cssStyleFromArray([
                'position' => 'absolute',
                'top' => '0',
                'left' => '0',
                'width' => '100%',
                'height' => '100%',
            ])),
        ]));
        return Html::tag('div', $iframe, ['id' => $this->getId(), 'class' => 'video video-youtube']);
    }
}

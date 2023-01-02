<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\embedVideo;

use yii\base\Widget;
use yii\helpers\Html;

use function http_build_query;
use function implode;
use function round;
use function sprintf;

class Twitch extends Widget
{
    public static $autoIdPrefix = 'w-twitch-';

    public $videoId;

    public $width = 847;
    public $height = null;

    public function run()
    {
        if ($this->height === null) {
            $this->height = round($this->width * 9 / 16);
        }

        $iframe = Html::tag('iframe', '', [
            'width' => (string)$this->width,
            'height' => (string)$this->height,
            'frameborder' => '0',
            'scrolling' => 'no',
            'allowfullscreen' => 'allowfullscreen',
            'src' => sprintf(
                'https://player.twitch.tv/?%s',
                http_build_query(['video' => "v{$this->videoId}"], '', '&'),
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
        return Html::tag('div', $iframe, ['id' => $this->getId(), 'class' => 'video video-twitch']);
    }
}

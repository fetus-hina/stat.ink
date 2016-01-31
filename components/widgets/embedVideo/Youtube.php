<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\widgets\embedVideo;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

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
                $params['start'] = (string)(($match[1] ?? 0) * 3600 + ($match[2] ?? 0) * 60 + ($match[3] ?? 0));
            }
        }
        $iframe = Html::tag('iframe', '', [
            'width'             => (string)$this->width,
            'height'            => (string)$this->height,
            'frameborder'       => '0',
            'allowfullscreen'   => 'allowfullscreen',
            'src' => sprintf(
                'https://%s/embed/%s?%s',
                $this->noCookie ? 'www.youtube-nocookie.com' : 'www.youtube.com',
                rawurlencode($this->videoId),
                http_build_query($params, '', '&')
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

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets\embedVideo;

use yii\base\Widget;
use yii\helpers\Html;

class Nicovideo extends Widget
{
    public static $autoIdPrefix = 'w-nico-';

    public $videoId;

    public $width = 847;
    public $height = null;

    public function run()
    {
        if ($this->height === null) {
            $this->height = round($this->width / 1.5);
        }

        return Html::tag(
            'div',
            Html::jsFile(
                sprintf(
                    'https://embed.nicovideo.jp/watch/%s/script?%s',
                    rawurlencode($this->videoId),
                    http_build_query(
                        [
                            'w' => (string)(int)$this->width,
                            'h' => (string)(int)$this->height,
                        ],
                        '',
                        '&',
                    ),
                ),
            ),
            ['id' => $this->id],
        );
    }
}

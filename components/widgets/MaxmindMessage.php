<?php

/**
 * @copyright Copyright (C) 2020-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use function vsprintf;

class MaxmindMessage extends Widget
{
    public $options = [
        'tag' => 'div',
        'class' => 'very-small',
    ];

    public function run()
    {
        $opt = $this->options;
        $tag = ArrayHelper::remove($opt, 'tag', 'div');

        return Html::tag(
            $tag,
            vsprintf('This product includes GeoLite2 data created by MaxMind, available from %s.', [
                Html::a(
                    'https://www.maxmind.com/',
                    'https://www.maxmind.com/',
                    [
                        'rel' => 'external nofollow',
                        'target' => '_blank',
                    ],
                ),
            ]),
            $opt,
        );
    }
}

<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\widgets;

use Yii;
use yii\base\Widget;
use yii\helpers\Html;

use function hash;
use function implode;
use function sprintf;

class WinLoseLegend extends Widget
{
    public function run()
    {
        $base = sprintf('legend-%s', hash('crc32b', self::class . '&' . $this->id));
        $mkLegend = function ($text, $color) use ($base) {
            $span = Html::tag('span', '', ['class' => "{$base}-bg", 'data' => ['color' => $color]]);
            return Html::tag(
                'div',
                $span . ' ' . Html::encode($text),
                ['class' => "{$base}-inner"],
            );
        };
        $html = Html::tag(
            'div',
            $mkLegend(Yii::t('app', 'Win'), 'win') . $mkLegend(Yii::t('app', 'Lose'), 'lose'),
            ['id' => "{$base}"],
        );

        $this->view->registerCss(implode("\n", [
            sprintf(
                '#%s{%s}',
                $base,
                Html::cssStyleFromArray([
                    'display' => 'inline-block',
                    'border' => '2px solid #ddd',
                    'padding' => '2px 5px',
                ]),
            ),
            sprintf(
                '#%1$s .%1$s-bg{%2$s}',
                $base,
                Html::cssStyleFromArray([
                    'display' => 'inline-block',
                    'width' => '1.618em',
                    'height' => '1em',
                    'line-height' => '1px',
                ]),
            ),
        ]));

        $this->view->registerJs(
            '(function($){' .
                "\$('.{$base}-bg').each(function(){" .
                    "\$(this).css('background-color', window.colorScheme[\$(this).attr('data-color')]);" .
                '})' .
            '})(jQuery);',
        );
        return $html;
    }
}

<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets;

use Yii;
use app\models\Salmon2;
use jp3cki\yii2\flot\FlotAsset;
use jp3cki\yii2\flot\FlotResizeAsset;
use yii\base\Widget;
use yii\helpers\Html;
use yii\helpers\Json;

use function array_filter;
use function array_keys;
use function array_map;
use function array_reverse;
use function array_values;
use function count;
use function in_array;
use function preg_replace;
use function range;
use function str_replace;

use const SORT_DESC;

class SalmonHazardHistory extends Widget
{
    public $user;
    public $current;

    public function init()
    {
        parent::init();

        if (!$this->user) {
            $this->user = $this->current->user;
        }
    }

    public function run(): string
    {
        $history = Salmon2::find()
            ->andWhere(['and',
                ['user_id' => $this->user->id],
                ['<=', 'id', $this->current->id],
                ['shift_period' => (int)$this->current->shift_period],
                ['not', ['danger_rate' => null]],
            ])
            ->orderBy([
                'id' => SORT_DESC,
            ])
            ->limit(10)
            ->all();

        if (count($history) < 2) {
            return '';
        }

        // line
        $series1 = [
            'color' => '#f5a101',
            'data' => array_map(
                fn (Salmon2 $model, int $index): array => [$index, (float)$model->danger_rate],
                array_reverse($history), // 古い順に取得
                range(-1 * (count($history) - 1), 0), // 最新が 0 になるように
            ),
            'label' => Yii::t('app-salmon2', 'Hazard Level'),
            'lines' => [
                'show' => true,
            ],
            'points' => [
                'show' => false,
            ],
        ];

        // Japanese & Korean: 〇 ×
        // Other regions: ✓ ✗
        $japaneseStyle = in_array(
            preg_replace('/@.+$/', '', Yii::$app->language),
            ['ja-JP', 'ko-KR', 'ko-KP'],
            true,
        );

        // cleared
        $series2 = [
            'color' => '#3169b3',
            'data' => array_values(array_filter(array_map(
                fn (Salmon2 $model, int $index): ?array => $model->clear_waves >= 3
                        ? [$index, (float)$model->danger_rate]
                        : null,
                array_reverse($history), // 古い順に取得
                range(-1 * (count($history) - 1), 0), // 最新が 0 になるように
            ))),
            'lines' => [
                'show' => false,
            ],
            'points' => [
                'show' => true,
                'symbol' => $japaneseStyle ? 'clear_ja' : 'clear',
            ],
        ];

        // failed
        $series3 = [
            'color' => '#ec6110',
            'data' => array_values(array_filter(array_map(
                fn (Salmon2 $model, int $index): ?array => $model->clear_waves < 3
                        ? [$index, (float)$model->danger_rate]
                        : null,
                array_reverse($history), // 古い順に取得
                range(-1 * (count($history) - 1), 0), // 最新が 0 になるように
            ))),
            'lines' => [
                'show' => false,
            ],
            'points' => [
                'show' => true,
                'symbol' => 'fail',
            ],
        ];

        $js = <<<'EOF'
(function ($) {
  function processRawData(plot, series, datapoints) {
    var handlers = {
      clear_ja: function (ctx, x, y, radius, shadow) {
        ctx.arc(x, y, radius * 1.5, 0, 2 * Math.PI);
      },
      clear: function (ctx, x, y, radius, shadow) {
        var size = radius * Math.sqrt(Math.PI) / 2 * (1.5 * 1.2);
        ctx.moveTo(x - size / 2, y);
        ctx.lineTo(x, y + size);
        ctx.moveTo(x, y + size);
        ctx.lineTo(x + size, y - size);
        ctx.moveTo(0, 0);
      },
      fail: function (ctx, x, y, radius, shadow) {
        var size = radius * Math.sqrt(Math.PI) / 2 * (1.5 * 1.2);
        ctx.moveTo(x - size, y - size);
        ctx.lineTo(x + size, y + size);
        ctx.moveTo(x - size, y + size);
        ctx.lineTo(x + size, y - size);
      },
    };

    var s = series.points.symbol;
    if (handlers[s])
      series.points.symbol = handlers[s];
    }

    function init(plot) {
      plot.hooks.processDatapoints.push(processRawData);
    }

    $.plot.plugins.push({
      init: init,
      name: 'hazard-symbols',
      version: '1.0'
    });
})(jQuery);
EOF;
        $this->view->registerJs($js);

        $flotOptions = <<<'EOF'
{
  legend: {
    show: false,
  },
  xaxis: {
    tickSize: 1,
    minTickSize: 1,
    show: false,
  },
  yaxis: {
    min: 0.0,
    max: 200.5,
    minTickSize: 0.5,
    tickFormatter: function (value, obj) {
      return Number(value).toFixed(1);
    },
  },
}
EOF;
        $replMap = [
            '{selector}' => '#' . $this->id,
            '{json}' => Json::encode([$series1, $series2, $series3], 0),
            '{options}' => $flotOptions,
        ];

        FlotAsset::register($this->view);
        FlotResizeAsset::register($this->view);
        $this->view->registerJs(str_replace(
            array_keys($replMap),
            array_values($replMap),
            'jQuery.plot("{selector}", {json}, {options});',
        ));

        return Html::tag('div', '', [
            'id' => $this->id,
            'style' => [
                'margin-top' => '10px',
                'width' => '100%',
                'height' => '150px',
            ],
        ]);
    }
}

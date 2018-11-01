<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
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

class SalmonHazardHistory extends Widget
{
    public $user;
    public $current;

    public function init()
    {
        parent::init();

        if (!$this->user) {
            $this->user = $current->user;
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

        $data1 = array_map(
            function (Salmon2 $model, int $index): array {
                return [$index, (float)$model->danger_rate];
            },
            array_reverse($history), // 古い順に取得
            range(-1 * (count($history) - 1), 0) // 最新が 0 になるように
        );
        $series1 = [
            'color' => '#ec6110',
            'data' => $data1,
            'label' => Yii::t('app-salmon2', 'Hazard Level'),
            'lines' => [
                'show' => true,
            ],
            'points' => [
                'show' => true,
            ],
        ];

        
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
            '{json}' => Json::encode([$series1], 0),
            '{options}' => $flotOptions,
        ];

        FlotAsset::register($this->view);
        FlotResizeAsset::register($this->view);
        $this->view->registerJs(str_replace(
            array_keys($replMap),
            array_values($replMap),
            'jQuery.plot("{selector}", {json}, {options});'
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

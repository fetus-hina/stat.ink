<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\RatioAsset;
use app\models\SalmonWaterLevel2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array<int, int> $values
 */

RatioAsset::register($this);

echo Html::tag(
  'div',
  Html::tag(
    'div',
    '',
    [
      'class' => 'tide-pie-chart',
      'data' => [
        'values' => Json::encode($values),
        'labels' => Json::encode(
          ArrayHelper::getColumn(
            $tides,
            fn (SalmonWaterLevel2 $model): string => Yii::t('app-salmon-tide2', $model->name),
          ),
        ),
      ],
    ],
  ),
  [
    'class' => [
      'mb-1',
      'ratio',
      'ratio-1x1',
    ],
  ],
);

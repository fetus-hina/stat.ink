<?php

declare(strict_types=1);

use app\assets\RatioAsset;
use app\assets\SimpleWinLosePieAsset;
use app\components\helpers\TypeHelper;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var View $this
 * @var array $stats
 */

$n = TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'battles'));
$win = (int)TypeHelper::intOrNull(ArrayHelper::getValue($stats, 'wins'));
if ($n < 1) {
  return;
}

RatioAsset::register($this);
SimpleWinLosePieAsset::register($this);

$this->registerJs('$(".simple-win-lose-pie").simpleWinLosePie();');

echo Html::tag(
  'div',
  Html::tag('div', '', [
    'class' => 'simple-win-lose-pie',
    'data' => [
      'labels' => Json::encode([
        'win' => Yii::t('app', 'Win'),
        'lose' => Yii::t('app', 'Lose'),
      ]),
      'values' => Json::encode([
        'win' => $win,
        'lose' => $n - $win,
      ]),
    ],
  ]),
  ['class' => 'ratio ratio-1x1 mb-1'],
);

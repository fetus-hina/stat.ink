<?php

declare(strict_types=1);

use app\assets\RatioAsset;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $battles
 * @var int $wins
 */

RatioAsset::register($this);

$fmt = Yii::$app->formatter;

echo Html::tag(
  'td',
  Html::tag(
    'div',
    Html::tag(
      'div',
      Html::tag(
        'div',
        Html::encode($battles > 0 ? $fmt->asPercent($wins / $battles, 0) : '-'),
        ['class' => 'flex-grow-1'],
      ),
      ['class' => 'd-flex align-items-center'],
    ),
    [
      'class' => 'auto-tooltip ratio ratio-1x1',
      'title' => $battles > 0
        ? vsprintf("%s: %s / %s", [
          $fmt->asPercent((int)$wins / (int)$battles, 2),
          $fmt->asInteger((int)$wins),
          $fmt->asInteger((int)$battles),
        ])
        : false,
    ],
  ),
  [
    'class' => [
      'kdcell',
      'p-0',
      'percent-cell',
      'text-center',
    ],
    'data' => [
      'battle' => (string)(int)$battles,
      'percent' => (string)(float)($battles > 0 ? ($wins * 100 / $battles) : ''),
    ],
  ],
);

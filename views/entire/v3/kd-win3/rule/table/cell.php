<?php

declare(strict_types=1);

use app\components\helpers\StandardError;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $battles
 * @var int $wins
 */

$fmt = Yii::$app->formatter;
$stderr = StandardError::winpct($wins, $battles);

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
        ? vsprintf("%s (%s): %s / %s", [
          $fmt->asPercent((int)$wins / (int)$battles, 2),
          $stderr
            ? Yii::t('app', '{from} - {to}', [
              'from' => $fmt->asPercent($stderr['min99ci'], 2),
              'to' => $fmt->asPercent($stderr['max99ci'], 2),
            ])
            : '?',
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

<?php

declare(strict_types=1);

use app\components\helpers\StandardError;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array{battles: int, wins: int}|null $data
 */

$fmt = Yii::$app->formatter;

$battles = $data['battles'] ?? 0;
$wins = $data['wins'] ?? 0;

$errData = StandardError::winpct($wins, $battles);
if (!$errData) {
  echo Html::tag(
    'td',
    Html::encode($battles > 0 ? $fmt->asPercent($wins / $battles, 2) : ''),
    ['class' => 'text-right'],
  );
  return;
}

echo Html::tag(
  'td',
  Html::encode($fmt->asPercent($errData['rate'], 2)),
  [
    'class' => ['auto-tooltip', 'text-right'],
    'title' => Yii::t('app', '{from} - {to}', [
      'from' => $fmt->asPercent($errData['min99ci'], 2),
      'to' => $fmt->asPercent($errData['max99ci'], 2),
    ]),
  ],
);

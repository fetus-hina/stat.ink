<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\StatSpecialUse3;
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var StatSpecialUse3|null $model
 * @var View $this
 * @var float|null $maxAvgUses
 */

$fmt = Yii::$app->formatter;

if (!$model) {
  return;
}

$labelHtml = Html::tag(
  'span',
  Html::encode($fmt->asDecimal($model->avg_uses, 2)),
  [
    'class' => 'auto-tooltip',
    'title' => vsprintf('%s: %s', [
      Yii::t('app', 'Standard Deviation'),
      $fmt->asDecimal($model->stddev, 2),
    ]),
  ],
);

echo ($maxAvgUses === null || $maxAvgUses <= 0.0)
  ? $labelHtml
  : Progress::widget([
    'barOptions' => ['class' => 'progress-bar-info'],
    'label' => $labelHtml,
    'percent' => 100.0 * $model->avg_uses / $maxAvgUses,
  ]);

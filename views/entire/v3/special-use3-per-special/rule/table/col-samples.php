<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\helpers\StandardError;
use yii\bootstrap\Progress;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array{battles: int, wins: int}|null $data
 * @var int $maxSamples
 */

$fmt = Yii::$app->formatter;

$battles = $data['battles'] ?? 0;

echo Html::tag(
  'td',
  Html::encode($fmt->asInteger($battles)),
  ['class' => 'text-right'],
);

echo Html::tag(
  'td',
  $maxSamples > 0
    ? Progress::widget([
      'barOptions' => ['class' => 'progress-bar-success'],
      'label' => '',
      'percent' => $battles * 100 / $maxSamples,
    ])
    : '',
  ['style' => ['min-width' => '80px']],
);

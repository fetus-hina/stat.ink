<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $n
 */

echo Html::tag(
  'p',
  Html::encode(
    \vsprintf('n = %s', [
      Yii::$app->formatter->asInteger($n),
    ]),
  ),
  [
    'class' => [
      'mb-2',
      'mt-0',
      'small',
      'text-center',
      'text-muted',
    ],
  ],
);

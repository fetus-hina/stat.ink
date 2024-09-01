<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $n
 */

echo Html::tag(
  'div',
  vsprintf('n = %s', [
    Yii::$app->formatter->asInteger($n),
  ]),
  [
    'class' => [
      'font-italic',
      'mt-1',
      'small',
      'text-center',
      'text-muted',
    ],
  ],
);

<?php

declare(strict_types=1);

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $count
 */

if (!$count) {
  echo Html::tag('td', '', ['data-sort-value' => '0']);
  return;
}

echo Html::tag(
  'td',
  Html::encode(Yii::$app->formatter->asInteger($count)),
  [
    'class' => 'text-right',
    'data' => [
      'sort-value' => $count,
    ],
  ],
);

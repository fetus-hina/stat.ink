<?php

declare(strict_types=1);

use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

echo Html::tag(
  'div',
  $model->start_at
    ? Yii::$app->formatter->asHtmlDatetime($model->start_at, 'short')
    : Html::encode('?'),
  ['class' => 'simple-battle-at'],
);

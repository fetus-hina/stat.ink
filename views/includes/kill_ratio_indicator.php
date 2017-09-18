<?php
use yii\helpers\Html;

if ($value == 1.0) {
  echo Html::tag(
    'span',
    '=',
    ['class' => 'label label-default']
  );
} elseif ($value > 1.0) {
  echo Html::tag(
    'span',
    Html::encode('>'),
    ['class' => 'label label-success']
  );
} else {
  echo Html::tag(
    'span',
    Html::encode('<'),
    ['class' => 'label label-danger']
  );
}

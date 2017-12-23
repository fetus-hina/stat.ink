<?php
use yii\helpers\Html;
?>
<?= Html::tag(
  'div',
  '',
  [
    'class' => 'graph',
    'data' => [
      'ref' => 'posts2',
      'label-battle' => Yii::t('app', 'Battles'),
      'label-user' => Yii::t('app', 'Users'),
    ],
  ]
) . "\n" ?>

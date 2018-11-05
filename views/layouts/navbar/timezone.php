<?php
use app\components\widgets\FA;
use yii\helpers\Html;
?>
<?= Html::a(
  implode('', [
    FA::fas('clock')->fw()->__toString(),
    Html::encode(Yii::t('app', 'Time Zone')),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  'javascript:;',
  [
    'data' => [
      'toggle' => 'modal',
      'target' => '#timezone-dialog',
    ],
    'role' => 'button',
    'aria-haspopup' => 'true',
    'aria-expanded' => 'false',
  ]
) . "\n" ?>

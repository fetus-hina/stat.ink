<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\components\helpers\Html;

?>
<?= Html::a(
  implode('', [
    FA::fas('clock')->fw()->__toString(),
    Html::encode(Yii::t('app', 'Time Zone')),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  '#timezone-dialog',
  [
    'data' => [
      'toggle' => 'modal',
    ],
    'role' => 'button',
    'aria-haspopup' => 'true',
    'aria-expanded' => 'false',
  ]
) . "\n" ?>

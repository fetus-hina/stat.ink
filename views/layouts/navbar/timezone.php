<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

?>
<?= Html::a(
  implode(' ', [
    Icon::timezone(),
    Html::encode(Yii::t('app', 'Time Zone')),
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

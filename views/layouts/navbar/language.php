<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\components\helpers\Html;
?>
<?= Html::a(
  implode('', [
    FA::fas('language')->fw()->__toString(),
    Html::encode('Language'),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  '#language-dialog',
  [
    'role' => 'button',
    'data' => [
      'toggle' => 'modal',
    ],
    'aria-haspopup' => 'true',
    'aria-expanded' => 'false',
  ]
) . "\n" ?>

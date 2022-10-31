<?php

declare(strict_types=1);

use app\components\widgets\FA;
use yii\helpers\Html;

?>
<?= Html::a(
  implode('', [
    (string)FA::fas('language')->fw(),
    Html::encode('Language'),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  '#language-dialog',
  [
    'aria' => [
      'expanded' => 'false',
      'haspopup' => 'true',
    ],
    'class' => [
      'lang-en',
      'lang-en-us',
    ],
    'data' => [
      'toggle' => 'modal',
    ],
    'lang' => 'en-US',
    'role' => 'button',
  ],
) . "\n" ?>

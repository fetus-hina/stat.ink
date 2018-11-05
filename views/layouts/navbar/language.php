<?php
declare(strict_types=1);

use app\components\widgets\FA;
use yii\helpers\Html;
?>
<?= Html::a(
  implode('', [
    FA::fas('language')->fw()->__toString(),
    Html::encode('Language'),
    ' ',
    Html::tag('span', '', ['class' => 'caret']),
  ]),
  'javascript:;',
  [
    'role' => 'button',
    'data' => [
      'toggle' => 'modal',
      'target' => '#language-dialog',
    ],
    'aria-haspopup' => 'true',
    'aria-expanded' => 'false',
  ]
) . "\n" ?>

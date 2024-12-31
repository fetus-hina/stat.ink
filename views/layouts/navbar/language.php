<?php

/**
 * @copyright Copyright (C) 2017-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

?>
<?= Html::a(
  implode(' ', [
    Icon::language(),
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

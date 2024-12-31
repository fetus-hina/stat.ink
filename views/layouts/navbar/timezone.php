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

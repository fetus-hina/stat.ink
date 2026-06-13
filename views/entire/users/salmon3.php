<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

use yii\helpers\Html;

?>
<?= Html::tag(
  'div',
  '',
  [
    'class' => 'graph',
    'data' => [
      'ref' => 'postsSalmon3',
      'count-key' => 'job',
      'label-battle' => Yii::t('app-salmon2', 'Jobs'),
      'label-user' => Yii::t('app', 'Users'),
    ],
  ]
) . "\n" ?>

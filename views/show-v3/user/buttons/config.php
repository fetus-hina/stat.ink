<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

?>
<?= Html::a(
  implode(' ', [
    Icon::listConfig(),
    Html::encode(Yii::t('app', 'View Settings')),
  ]),
  '#table-config',
  [
    'class' => [
      'btn',
      'btn-default',
    ],
  ],
) ?>

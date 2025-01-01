<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var int $count
 */

if (!$count) {
  echo Html::tag('td', '', ['data-sort-value' => '0']);
  return;
}

echo Html::tag(
  'td',
  Html::encode(Yii::$app->formatter->asInteger($count)),
  [
    'class' => 'text-right',
    'data' => [
      'sort-value' => $count,
    ],
  ],
);

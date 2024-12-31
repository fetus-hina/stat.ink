<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

return [
  'attribute' => 'defeated_by_me',
  'contentOptions' => fn (array $row): array => [
    'class' => 'text-right',
    'data-sort-value' => (int)ArrayHelper::getValue($row, 'defeated_by_me'),
  ],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => [
    'class' => 'auto-tooltip text-center',
    'data' => [
      'sort' => 'int',
      'sort-default' => 'desc',
      'sort-onload' => 'yes',
    ],
    'title' => Yii::t('app-salmon3', 'Defeated by {user}', ['user' => $user->name]),
  ],
  'label' => Html::encode($user->name),
];

<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3FilterForm;
use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var Weapon3 $weapon
 * @var array<string, array>|null $stats
 */

$totalBattles = 0;
if ($stats) {
  foreach ($stats as $info) {
    $totalBattles += (int)ArrayHelper::getValue($info, 'battles');
  }
}

echo Html::tag(
  'td',
  implode('', [
    Html::tag(
      'div',
      $this->render('cell-weapon/main', ['model' => $weapon, 'user' => $user]),
      [
        'style' => 'max-width: 4em',
      ],
    ),
  ]),
  [
    'class' => 'text-center',
    'data' => [
      'sort-value' => $totalBattles,
    ],
  ],
);

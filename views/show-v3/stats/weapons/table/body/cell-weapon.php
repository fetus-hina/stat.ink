<?php

declare(strict_types=1);

use app\assets\Spl3WeaponAsset;
use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\components\widgets\v3\weaponIcon\SubweaponIcon;
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
      ['class' => 'mb-1'],
    ),
    Html::tag(
      'div',
      implode(' ', [
        $this->render('cell-weapon/subweapon', ['model' => $weapon->subweapon, 'user' => $user]),
        $this->render('cell-weapon/special', ['model' => $weapon->special, 'user' => $user]),
      ]),
    ),
  ]),
  [
    'class' => 'text-center',
    'data' => [
      'sort-value' => $totalBattles,
    ],
  ],
);

<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SubweaponIcon;
use app\models\Battle3FilterForm;
use app\models\Subweapon3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Subweapon3 $model
 * @var User $user
 * @var View $this
 */

echo Html::a(
  SubweaponIcon::widget(['model' => $model]),
  ['show-v3/user',
    'screen_name' => $user->screen_name,
    'f' => [
      'weapon' => implode('', [
        Battle3FilterForm::PREFIX_WEAPON_SUB,
        $model->key,
      ]),
      'result' => '~win_lose',
    ],
  ],
);

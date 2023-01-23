<?php

declare(strict_types=1);

use app\components\widgets\v3\weaponIcon\SpecialIcon;
use app\models\Battle3FilterForm;
use app\models\Special3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Special3 $model
 * @var User $user
 * @var View $this
 */

echo Html::a(
  SpecialIcon::widget(['model' => $model]),
  ['show-v3/user',
    'screen_name' => $user->screen_name,
    'f' => [
      'weapon' => implode('', [
        Battle3FilterForm::PREFIX_WEAPON_SPECIAL,
        $model->key,
      ]),
      'result' => '~win_lose',
    ],
  ],
);

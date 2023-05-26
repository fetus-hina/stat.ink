<?php

declare(strict_types=1);

use app\models\Battle3FilterForm;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var Weapon3 $model
 */

echo Html::a(
  Yii::t('app-weapon3', $model->name),
  ['show-v3/user',
    'screen_name' => $user->screen_name,
    'f' => [
      'weapon' => $model->key,
      'result' => '~win_lose',
    ],
  ],
  [
    'style' => [
      'max-width' => '4em',
      'word-break' => 'break-all',
    ],
  ],
);

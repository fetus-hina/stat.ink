<?php

declare(strict_types=1);

use app\models\Battle3;
use app\models\User;
use yii\bootstrap\Html;

/**
 * @var Battle3 $model
 * @var User $user
 * @var View $this
 */

$currentUser = Yii::$app->user->identity;
if ($currentUser && (int)$currentUser->id === (int)$user->id) {
  echo Html::tag(
    'p',
    Html::button(
      Html::encode(Yii::t('app', 'Edit')),
      [
        'class' => 'btn btn-default auto-tooltip',
        'disabled' => true,
        'title' => 'This feature not implemented yet ' . mb_chr(0x1f647, 'UTF-8'),
      ]
    ),
    // Html::a(
    //   Html::encode(Yii::t('app', 'Edit')),
    //   ['/show-v3/edit-battle',
    //     'screen_name' => $user->screen_name,
    //     'battle' => $model->uuid,
    //   ],
    //   ['class' => 'btn btn-default']
    // ),
    ['class' => 'text-right']
  );
}

<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

echo Html::tag(
  'p',
  Html::a(
    vsprintf('%s %s %s', [
      (string)FA::fas('paint-roller')->fw(),
      Html::encode(Yii::t('app', 'Battles')),
      (string)FA::fas('angle-right')->fw(),
    ]),
    ['show-v3/user',
      'screen_name' => $user->screen_name,
    ],
    ['class' => 'btn btn-default btn-xs'],
  ),
  ['class' => 'text-right'],
);

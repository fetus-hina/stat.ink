<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

$displayName = $user->loginWithGoogle->name;
$email = $user->loginWithGoogle->email;

echo implode(' ', [
  Html::encode($displayName),
  $email !== null && $email !== ''
    ? sprintf('(%s)', Html::encode($email))
    : '',
  Html::a(
    implode(' ', [
      Icon::appLink(),
      Html::encode(Yii::t('app', 'Another account')),
    ]),
    ['update-login-with-google'],
    ['class' => 'btn btn-primary'],
  ),
  Html::a(
    implode('', [
      Icon::appUnlink(),
      Html::encode(Yii::t('app', 'Unlink account')),
    ]),
    ['clear-login-with-google'],
    ['class' => 'btn btn-danger'],
  ),
]);

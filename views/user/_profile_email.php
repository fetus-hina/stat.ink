<?php

declare(strict_types=1);

use app\components\widgets\FA;
use app\models\User;
use app\components\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

echo Html::encode(Yii::t(
  'app',
  'We\'ll send an email when you log in to the website or change your password.'
)) . '<br>';

if ($user->email) {
    echo Html::tag('code', Html::encode($user->email));
    echo ' ';
    echo Html::encode(sprintf('(%s)', Html::encode($user->emailLang->name ?? '?')));
    echo ' ';
}
echo Html::a(
  implode(' ', [
    FA::fas('redo')->fw(),
    Html::encode(Yii::t('app', 'Update')),
  ]),
  ['user/edit-email'],
  ['class' => 'btn btn-default']
);

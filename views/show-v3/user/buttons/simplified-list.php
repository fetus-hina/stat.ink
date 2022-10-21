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

?>
<?= Html::a(
  implode(' ', [
    (string)FA::fas('list')->fw(),
    Html::encode(Yii::t('app', 'Simplified List')),
  ]),
  ['show-v3/user',
    'screen_name' => $user->screen_name,
    'v' => 'simple',
  ],
  [
    'class' => [
      'btn',
      'btn-default',
    ],
    'rel' => 'nofollow',
  ],
) ?>

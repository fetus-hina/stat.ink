<?php

declare(strict_types=1);

use app\components\widgets\v3\BattleDeleteWidget;
use app\models\Salmon3;
use app\models\User;
use yii\bootstrap\Html;

/**
 * @var Salmon3 $model
 * @var User $user
 * @var View $this
 */

$currentUser = Yii::$app->user->identity;
if ($currentUser && (int)$currentUser->id === (int)$user->id) {
  echo Html::tag(
    'div',
    implode('', [
      BattleDeleteWidget::widget(['model' => $model]),
    ]),
    ['class' => 'text-right'],
  );
}

<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\v3\BattleDeleteWidget;
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
    'div',
    implode('', [
      BattleDeleteWidget::widget(['model' => $model]),
    ]),
    ['class' => 'text-right'],
  );
}

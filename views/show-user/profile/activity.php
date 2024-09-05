<?php

declare(strict_types=1);

use app\components\widgets\ActivityWidget;
use app\models\User;
use yii\bootstrap\Html;

/**
 * @var User $user
 */

if (Yii::$app->request->isPjax) {
  return;
}

?>
<div class="panel panel-default">
  <div class="panel-heading">
    <?= Html::encode(Yii::t('app', 'Activity')) . "\n" ?>
  </div>
  <div class="panel-body bg-white text-body">
    <div class="table-responsive">
      <?= ActivityWidget::widget(['user' => $user]) . "\n" ?>
    </div>
  </div>
</div>

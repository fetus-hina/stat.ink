<?php
declare(strict_types=1);

use app\components\helpers\Html;

if (!$model->isEditable) {
  return;
}
?>
<div class="text-right" style="margin-bottom:15px">
  <?= Html::a(
    Html::encode(Yii::t('app', 'Edit')),
    ['salmon/edit',
      'screen_name' => $model->user->screen_name,
      'id' => $model->id,
    ],
    [
      'class' => 'btn btn-default',
      'rel' => 'nofollow',
    ]
  ) . "\n" ?>
</div>

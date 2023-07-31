<?php

declare(strict_types=1);

use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <?= Html::encode(Yii::t('app', 'Reset your password')) . "\n" ?>
  </div>
  <div class="panel-body pb-0">
    <?= Html::a(
      Html::encode(Yii::t('app', 'If you know our API Token')),
      ['/user/reset-password-apikey'],
      [
        'class' => 'btn btn-default btn-block mb-3',
        'rel' => 'nofollow',
      ],
    ) . "\n" ?>
  </div>
</div>

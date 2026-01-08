<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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
    <div class="alert alert-danger mb-3">
      <?= implode('<br>', [
        Html::encode(Yii::t('app', 'This feature is disabled by default.')),
        Html::encode(Yii::t('app', 'To change your password using this feature, contact the administrator first.')),
      ]) . "\n" ?>
    </div>
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

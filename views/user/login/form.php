<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\LoginForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var LoginForm $login
 * @var View $this
 */

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <h1 class="panel-title">
      <?= Html::encode(Yii::t('app', 'Login')) . "\n" ?>
    </h1>
  </div>
  <div class="panel-body pb-0">
    <?php $_ = ActiveForm::begin(['id' => 'login-form']); echo "\n" ?>
      <?= $_->field($login, 'screen_name')
        ->textInput([
          'autocomplete' => 'username',
        ]) . "\n"
      ?>
      <?= $_->field($login, 'password')
        ->passwordInput([
          'autocomplete' => 'current-password',
        ]) . "\n"
      ?>
      <?= $_->field($login, 'remember_me')
        ->checkbox() . "\n"
      ?>
      <div class="form-group mb-3">
        <?= Html::submitButton(
          Html::encode(Yii::t('app', 'Login')),
          ['class' => 'btn btn-primary btn-block']
        ) . "\n" ?>
      </div>
      <div class="form-group mb-3">
        <?= Html::a(
          Html::encode(Yii::t('app', 'Register')),
          ['/user/register'],
          ['class' => 'btn btn-default btn-block']
        ) . "\n" ?>
      </div>
    <?php ActiveForm::end(); echo "\n" ?>
  </div>
</div>

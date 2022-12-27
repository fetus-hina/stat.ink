<?php

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\components\widgets\Icon;
use app\models\LoginForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var LoginForm $login
 * @var View $this
 */

$this->title = implode(' | ', [
    Yii::$app->name,
    Yii::t('app', 'Login'),
]);
?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h1 class="panel-title">
            <?= Html::encode(Yii::t('app', 'Login')) . "\n" ?>
          </h1>
        </div>
        <div class="panel-body">
          <?php $_ = ActiveForm::begin(['id' => 'login-form']); echo "\n" ?>
            <?= $_->field($login, 'screen_name') . "\n" ?>
            <?= $_->field($login, 'password')->passwordInput() . "\n" ?>
            <?= $_->field($login, 'remember_me')
              ->checkbox() . "\n"
            ?>
            <div class="form-group">
              <?= Html::submitButton(
                Html::encode(Yii::t('app', 'Login')),
                ['class' => 'btn btn-primary btn-block']
              ) . "\n" ?>
            </div>
            <div class="form-group">
              <?= Html::a(
                Html::encode(Yii::t('app', 'Register')),
                ['/user/register'],
                ['class' => 'btn btn-default btn-block']
              ) . "\n" ?>
            </div>
          <?php ActiveForm::end(); echo "\n" ?>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <h2 class="panel-title">
            <?= Html::encode(Yii::t('app', 'Log in with other services')) . "\n" ?>
          </h2>
        </div>
        <div class="panel-body">
          <div class="form-group">
<?php $_provided = false ?>
<?php if (Yii::$app->params['twitter']['read_enabled'] ?? false) { ?>
            <?= Html::a(
              implode(' ', [
                Icon::twitter(),
                Html::encode(Yii::t('app', 'Log in with Twitter')),
              ]),
              ['/user/login-with-twitter'],
              [
                'class' => 'btn btn-info btn-block',
                'rel' => 'nofollow',
              ]
            ) . "\n" ?>
<?php $_provided = true ?>
<?php } ?>
<?php if (!$_provided) { ?>
            <p>
              <?= Html::encode(
                Yii::t('app', 'No service configured by the system administrator.')
              ) . "\n" ?>
            </p>
<?php } ?>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>

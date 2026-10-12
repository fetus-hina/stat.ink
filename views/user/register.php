<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\user\RegisterAction;
use app\components\widgets\AdWidget;
use app\models\RegisterForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var RegisterForm $register
 * @var View $this
 * @var string $method
 */

$this->title = implode(' | ', [
  Yii::$app->name,
  Yii::t('app-user', 'Register'),
]);

?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <h1>
        <?= Html::encode(Yii::t('app-user', 'Register')) . "\n" ?>
      </h1>
      <?= Html::a(
        Yii::t('app-user', 'If you already have an account, please click here.'),
        ['user/login'],
        []
      ) . "\n" ?>
      <?= $method === RegisterAction::METHOD_PASSWORD
        ? $this->render('register/password', compact('register'))
        : $this->render('register/passkey', compact('register'))
      ?>
      <?= Html::tag(
        'div',
        Html::a(
          Html::encode(Yii::t('app', 'Login')),
          ['/user/login'],
          ['class' => 'btn btn-default btn-block']
        ),
        ['style' => [
          'margin-top' => '15px',
        ]]
      ) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-6 col-md-6 col-lg-6" style="padding:0 5%">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>

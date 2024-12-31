<?php

/**
 * @copyright Copyright (C) 2018-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\DetailView;

/**
 * @var User $user
 * @var View $this
 */

$title = Yii::t('app', 'Profile and Settings');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$this->registerCss('.btn-block.text-left{text-align:left}');
?>
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-sm-9">
      <h1>
        <?= Html::encode($title) . "\n" ?>
        <?= Html::a(
          implode(' ', [
            Icon::edit(),
            Html::encode(Yii::t('app', 'Update')),
          ]),
          ['edit-profile'],
          ['class' => 'btn btn-primary'],
        ) . "\n" ?>
      </h1>
      <?= $this->render('profile/alert-versions') . "\n" ?>
      <?= $this->render('profile/profile', compact('user')) . "\n" ?>
      <?= $this->render('profile/login-with', compact('user')) . "\n" ?>
      <?= $this->render('profile/slack', compact('user')) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-3">
      <?= $this->render('profile/login-history') . "\n" ?>
      <?= $this->render('profile/exports', compact('user')) . "\n" ?>
    </div>
  </div>
</div>

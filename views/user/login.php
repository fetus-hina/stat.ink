<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\AdWidget;
use app\models\LoginForm;
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
    <div class="col-xs-12 col-sm-6 mb-3">
      <?= $this->render('login/form', compact('login')) . "\n" ?>
    </div>
    <div class="col-xs-12 col-sm-6 mb-3">
      <?= $this->render('login/integrated') . "\n" ?>
      <?= $this->render('login/forget') . "\n" ?>
    </div>
  </div>
  <div class="row">
    <div class="col-xs-12 mb-3">
      <?= AdWidget::widget() . "\n" ?>
    </div>
  </div>
</div>

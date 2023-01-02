<?php

declare(strict_types=1);

use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 */

?>
<h2><?= Html::encode(Yii::t('app', 'Log in with other services')) ?></h2>
<table class="table table-striped">
  <tbody>
    <?= $this->render('login-with/twitter', compact('user')) . "\n" ?>
  </tbody>
</table>

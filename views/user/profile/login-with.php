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
<div class="alert alert-warning">
  <p>
    <?= Html::encode(
      Yii::t(
        'app',
        'Since the acquisition of Twitter by Elon Musk, the environment for third-party applications has deteriorated rapidly and extremely.',
      ),
    ) . "\n" ?>
  </p>
  <p class="mb-0">
    <?= Html::encode(
      Yii::t(
        'app',
        'The future of Twitter integration is completely uncertain, and as a {site} developer I can\'t recommend this feature at this time.',
        [
          'site' => Yii::$app->name,
        ],
      ),
    ) . "\n" ?>
  </p>
</div>
<table class="table table-striped">
  <tbody>
    <?= $this->render('login-with/twitter', compact('user')) . "\n" ?>
  </tbody>
</table>

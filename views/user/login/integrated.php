<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$enableDiscord = Yii::$app->params['discord']['read_enabled'] ?? false;
$enableGoogle = Yii::$app->params['google']['read_enabled'] ?? false;
$enableTwitter = Yii::$app->params['twitter']['read_enabled'] ?? false;
$provided = $enableDiscord || $enableGoogle || $enableTwitter;

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <h2 class="panel-title">
      <?= Html::encode(Yii::t('app', 'Log in with other services')) . "\n" ?>
    </h2>
  </div>
  <div class="panel-body pb-0">
    <div class="form-group mb-3">
<?php if ($enableDiscord) { ?>
      <?= Html::a(
        implode(' ', [
          Icon::discord(),
          Html::encode(Yii::t('app', 'Log in with Discord')),
        ]),
        ['/user/login-with-discord'],
        [
          'class' => 'btn btn-primary btn-block mb-2',
          'rel' => 'nofollow',
        ]
      ) . "\n" ?>
<?php } ?>
<?php if ($enableGoogle) { ?>
      <?= Html::a(
        implode(' ', [
          Icon::google(),
          Html::encode(Yii::t('app', 'Log in with Google')),
        ]),
        ['/user/login-with-google'],
        [
          'class' => 'btn btn-danger btn-block mb-2',
          'rel' => 'nofollow',
        ]
      ) . "\n" ?>
<?php } ?>
<?php if ($enableTwitter) { ?>
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
<?php } ?>
<?php if (!$provided) { ?>
      <p class="mb-3">
        <?= Html::encode(
          Yii::t('app', 'No service configured by the system administrator.')
        ) . "\n" ?>
      </p>
<?php } ?>
    </div>
  </div>
</div>

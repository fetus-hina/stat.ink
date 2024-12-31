<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

$enableTwitter = Yii::$app->params['twitter']['read_enabled'] ?? false;
$provided = $enableTwitter;

?>
<div class="panel panel-default mb-3">
  <div class="panel-heading">
    <h2 class="panel-title">
      <?= Html::encode(Yii::t('app', 'Log in with other services')) . "\n" ?>
    </h2>
  </div>
  <div class="panel-body pb-0">
    <div class="form-group mb-3">
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

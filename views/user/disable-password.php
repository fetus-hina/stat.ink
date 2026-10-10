<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var int $passkeyCount
 * @var string|null $errorMessage
 */

$title = Yii::t('app-user', 'Disable Password');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <p>
    <?= Html::encode(
      Yii::t(
        'app-user',
        'If you disable your password, you will sign in with your passkey only. Your password will be erased from the server.',
      ),
    ) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(
      Yii::t(
        'app-passkey',
        '{n, plural, =0{No passkeys registered} one{# passkey registered} other{# passkeys registered}}',
        ['n' => $passkeyCount],
      ),
    ) . "\n" ?>
  </p>
  <ul>
    <li>
      <?= Html::encode(
        Yii::t(
          'app-user',
          'You can set a password again from your profile page after verifying with your passkey.',
        ),
      ) . "\n" ?>
    </li>
    <li>
      <?= Html::encode(
        Yii::t(
          'app-passkey',
          'You cannot delete your last passkey while your password is disabled.',
        ),
      ) . "\n" ?>
    </li>
    <li>
      <?= Html::encode(
        Yii::t(
          'app-user',
          'If you lose all your passkeys, you can set a new password with a recovery key. We recommend creating recovery keys in advance.',
        ),
      ) . "\n" ?>
      <?= Html::a(
        Html::encode(Yii::t('app-recovery-key', 'Recovery Keys')),
        ['user/recovery-key'],
      ) . "\n" ?>
    </li>
  </ul>

  <?php if ($errorMessage !== null) { ?>
    <div class="alert alert-danger">
      <?= Html::encode($errorMessage) . "\n" ?>
    </div>
  <?php } ?>
  <?= $this->render('passkey-reauth/config') . "\n" ?>

  <?= Html::beginForm(['user/disable-password'], 'post', ['data-passkey-reauth' => 'form']) . "\n" ?>
    <?= Html::submitButton(
      implode(' ', [
        Icon::passkey(),
        Html::encode(Yii::t('app-user', 'Verify with your passkey and disable password')),
      ]),
      ['class' => 'btn btn-lg btn-danger btn-block'],
    ) . "\n" ?>
  <?= Html::endForm() . "\n" ?>

  <div style="margin-top:15px">
    <?= Html::a(
      Html::encode(Yii::t('app', 'Back')),
      ['user/profile'],
      ['class' => 'btn btn-lg btn-default btn-block'],
    ) . "\n" ?>
  </div>
</div>

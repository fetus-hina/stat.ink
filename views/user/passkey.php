<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\PasskeyAsset;
use app\components\widgets\Icon;
use app\models\User;
use app\models\UserPasskey;
use yii\db\ArrayExpression;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var User $user
 * @var UserPasskey[] $passkeys
 * @var View $this
 */

$title = Yii::t('app-passkey', 'Passkeys');
$this->title = implode(' | ', [
    Yii::$app->name,
    $title,
]);

PasskeyAsset::register($this);

$this->registerJs(sprintf(
    'window.__passkeyConfig = %s;',
    Json::encode([
        'urls' => [
            'start' => Url::to(['user/passkey-register-start']),
            'finish' => Url::to(['user/passkey-register-finish']),
            'delete' => Url::to(['user/passkey-delete']),
        ],
        'csrfParam' => Yii::$app->request->csrfParam,
        'csrfToken' => Yii::$app->request->csrfToken,
        'messages' => [
            'unsupported' => Yii::t('app-passkey', 'Your browser does not support passkeys.'),
            'registerFailed' => Yii::t('app-passkey', 'Failed to register passkey.'),
            'confirmDelete' => Yii::t('app-passkey', 'Are you sure you want to delete this passkey?'),
            'nicknameRequired' => Yii::t('app-passkey', 'Please enter a nickname.'),
        ],
    ]),
), View::POS_HEAD);

$transportsOf = function (UserPasskey $p): array {
    $v = $p->transports;
    if ($v instanceof ArrayExpression) {
        $v = $v->getValue();
    }
    return is_array($v) ? $v : [];
};
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <p>
    <?= Html::encode(Yii::t(
      'app-passkey',
      'Passkeys let you sign in without a password using your device\'s screen lock, a security key, or a cloud-synced credential.',
    )) . "\n" ?>
  </p>

  <div id="passkey-unsupported-alert" class="alert alert-warning" style="display:none">
    <?= Html::encode(Yii::t('app-passkey', 'Your browser does not support passkeys.')) . "\n" ?>
  </div>

  <h2><?= Html::encode(Yii::t('app-passkey', 'Registered Passkeys')) ?></h2>
  <?php if (empty($passkeys)) : ?>
    <p class="text-muted">
      <?= Html::encode(Yii::t('app-passkey', 'No passkeys registered yet.')) . "\n" ?>
    </p>
  <?php else : ?>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th><?= Html::encode(Yii::t('app-passkey', 'Nickname')) ?></th>
            <th><?= Html::encode(Yii::t('app-passkey', 'Transports')) ?></th>
            <th><?= Html::encode(Yii::t('app-passkey', 'Created At')) ?></th>
            <th><?= Html::encode(Yii::t('app-passkey', 'Last Used At')) ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php
          $fmt = Yii::$app->formatter;
          foreach ($passkeys as $passkey) : ?>
            <tr>
              <td><?= Html::encode($passkey->nickname) ?></td>
              <td><?= Html::encode(implode(', ', $transportsOf($passkey))) ?></td>
              <td><?= Html::encode($fmt->asDatetime($passkey->created_at, 'medium')) ?></td>
              <td>
                <?= Html::encode(
                  $passkey->last_used_at !== null
                    ? $fmt->asDatetime($passkey->last_used_at, 'medium')
                    : Yii::t('app-passkey', '(never)'),
                ) . "\n" ?>
              </td>
              <td>
                <?= Html::tag(
                  'button',
                  Html::encode(Yii::t('app-passkey', 'Delete')),
                  [
                    'type' => 'button',
                    'class' => 'passkey-delete btn btn-danger btn-sm',
                    'data' => ['id' => $passkey->id],
                  ],
                ) . "\n" ?>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>
  <hr>
  <h2><?= Html::encode(Yii::t('app-passkey', 'Register New Passkey')) ?></h2>
  <div class="form-group">
    <label for="passkey-nickname"><?= Html::encode(Yii::t('app-passkey', 'Nickname')) ?></label>
    <?= Html::textInput('nickname', '', [
      'id' => 'passkey-nickname',
      'class' => 'form-control',
      'maxlength' => 64,
      'placeholder' => Yii::t('app-passkey', 'e.g., "iPhone Face ID"'),
    ]) . "\n" ?>
  </div>
  <?= Html::tag(
    'button',
    implode(' ', [
      Icon::addSomething(),
      Html::encode(Yii::t('app-passkey', 'Register')),
    ]),
    [
      'type' => 'button',
      'id' => 'passkey-register-button',
      'class' => 'btn btn-block btn-lg btn-primary',
    ],
  ) . "\n" ?>

  <div id="passkey-message" class="mt-3" style="display:none"></div>
  <hr>
  <div style="margin-top:30px">
    <?= Html::a(
      Html::encode(Yii::t('app-passkey', 'Back')),
      ['user/profile'],
      ['class' => 'btn btn-block btn-lg btn-default'],
    ) . "\n" ?>
  </div>
</div>

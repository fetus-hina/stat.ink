<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\actions\user\RecoveryKeyCreateAction;
use app\components\widgets\Icon;
use app\models\User;
use app\models\UserPasswordRecoveryKey;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var UserPasswordRecoveryKey[] $recoveryKeys
 * @var string|null $justCreated
 * @var string|null $errorMessage
 * @var View $this
 */

$title = Yii::t('app-recovery-key', 'Recovery Keys');
$this->title = implode(' | ', [
  Yii::$app->name,
  $title,
]);

$fmt = Yii::$app->formatter;
$activeCount = count($recoveryKeys);
$canCreate = $activeCount < RecoveryKeyCreateAction::KEY_LIMIT;
?>
<div class="container">
  <h1><?= Html::encode($title) ?></h1>

  <p>
    <?= Html::encode(Yii::t(
      'app-recovery-key',
      'Recovery keys let you reset your password if you have lost access to your account.',
    )) . "\n" ?>
  </p>
  <p>
    <?= Html::encode(Yii::t(
      'app-recovery-key',
      'Each recovery key can be used only once. Store the keys somewhere safe.',
    )) . "\n" ?>
  </p>

  <?php if (is_string($errorMessage)) : ?>
    <div class="alert alert-danger">
      <?= Html::encode($errorMessage) . "\n" ?>
    </div>
  <?php endif ?>

  <?php if (is_string($justCreated)) : ?>
    <div class="alert alert-success">
      <h4><?= Html::encode(Yii::t('app-recovery-key', 'Your new recovery key')) ?></h4>
      <p>
        <strong>
          <?= Html::encode(Yii::t(
            'app-recovery-key',
            'This key will be shown only once. Save it now in a secure location.',
          )) . "\n" ?>
        </strong>
      </p>
      <pre style="word-break:break-all;white-space:pre-wrap"><?=
        Html::encode($justCreated) ?></pre>
    </div>
  <?php endif ?>

  <h2><?= Html::encode(Yii::t('app-recovery-key', 'Active Recovery Keys')) ?></h2>
  <?php if (empty($recoveryKeys)) : ?>
    <p class="text-muted">
      <?= Html::encode(Yii::t('app-recovery-key', 'No active recovery keys.')) . "\n" ?>
    </p>
  <?php else : ?>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th><?= Html::encode(Yii::t('app-recovery-key', 'Key ID')) ?></th>
            <th><?= Html::encode(Yii::t('app-recovery-key', 'Created At')) ?></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recoveryKeys as $key) : ?>
            <tr>
              <td><code><?= Html::encode($key->public_id) ?></code></td>
              <td><?= Html::encode($fmt->asDatetime($key->created_at, 'medium')) ?></td>
              <td>
                <?= Html::beginForm(['user/recovery-key-delete'], 'post', [
                  'style' => 'display:inline',
                ]) . "\n" ?>
                <?= Html::hiddenInput('id', (string)$key->id) . "\n" ?>
                <?= Html::submitButton(
                  Html::encode(Yii::t('app-recovery-key', 'Revoke')),
                  [
                    'class' => 'btn btn-danger btn-sm',
                    'data' => [
                      'confirm' => Yii::t(
                        'app-recovery-key',
                        'Are you sure you want to revoke this recovery key?',
                      ),
                    ],
                  ],
                ) . "\n" ?>
                <?= Html::endForm() . "\n" ?>
              </td>
            </tr>
          <?php endforeach ?>
        </tbody>
      </table>
    </div>
  <?php endif ?>

  <hr>
  <h2><?= Html::encode(Yii::t('app-recovery-key', 'Create a New Recovery Key')) ?></h2>
  <?php if ($canCreate) : ?>
    <?= Html::beginForm(['user/recovery-key-create'], 'post') . "\n" ?>
    <?= Html::submitButton(
      implode(' ', [
        Icon::addSomething(),
        Html::encode(Yii::t('app-recovery-key', 'Create')),
      ]),
      ['class' => 'btn btn-block btn-lg btn-primary'],
    ) . "\n" ?>
    <?= Html::endForm() . "\n" ?>
  <?php else : ?>
    <p class="text-muted">
      <?= Html::encode(Yii::t(
        'app-recovery-key',
        'You can have at most {n} active recovery keys.',
        ['n' => RecoveryKeyCreateAction::KEY_LIMIT],
      )) . "\n" ?>
    </p>
  <?php endif ?>

  <hr>
  <div style="margin-top:30px">
    <?= Html::a(
      Html::encode(Yii::t('app-recovery-key', 'Back')),
      ['user/profile'],
      ['class' => 'btn btn-block btn-lg btn-default'],
    ) . "\n" ?>
  </div>
</div>

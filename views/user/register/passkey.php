<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\actions\user\RegisterAction;
use app\assets\PasskeySignupAsset;
use app\components\widgets\Icon;
use app\models\RegisterForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var RegisterForm $register
 * @var View $this
 */

PasskeySignupAsset::register($this);

$this->registerJs(sprintf(
  'window.__passkeySignupConfig = %s;',
  Json::encode([
    'urls' => [
      'start' => Url::to(['user/passkey-signup-start']),
      'finish' => Url::to(['user/passkey-signup-finish']),
    ],
    'messages' => [
      'registerFailed' => Yii::t('app-passkey', 'Failed to register passkey.'),
    ],
  ]),
), View::POS_HEAD);

$passwordUrl = ['user/register', 'method' => RegisterAction::METHOD_PASSWORD];

?>
<p>
  <?= Html::encode(Yii::t(
    'app-passkey',
    'Passkeys let you sign in without a password using your device\'s screen lock, a security key, or a cloud-synced credential.',
  )) . "\n" ?>
</p>
<div id="passkey-signup-unsupported" class="alert alert-warning" style="display:none">
  <?= Html::encode(Yii::t('app-passkey', 'Your browser does not support passkeys.')) . "\n" ?>
  <?= Html::a(
    Html::encode(Yii::t('app-user', 'Register with a password instead')),
    $passwordUrl,
    ['class' => 'alert-link'],
  ) . "\n" ?>
</div>
<?php $_ = ActiveForm::begin(['id' => 'register-passkey-form']); echo "\n" ?>
  <?= $_->field($register, 'name')
    ->textInput(['autocomplete' => 'nickname']) . "\n"
  ?>
  <?= $_->field($register, 'screen_name')
    ->textInput(['autocomplete' => 'username'])
    ->hint(Yii::t('app-user', 'This will be made public as part of URL')) . "\n"
  ?>
  <?= Html::submitButton(
    implode(' ', [
      Icon::passkey(),
      Html::encode(Yii::t('app-user', 'Register with Passkey')),
    ]),
    [
      'id' => 'passkey-signup-button',
      'class' => 'btn btn-primary btn-block',
    ],
  ) . "\n" ?>
<?php ActiveForm::end(); echo "\n" ?>
<div id="passkey-signup-message" class="mt-3" role="alert" style="display:none"></div>
<p class="text-center" style="margin-top:15px">
  <?= Html::a(
    Html::encode(Yii::t('app-user', 'Register with a password instead')),
    $passwordUrl,
  ) . "\n" ?>
</p>

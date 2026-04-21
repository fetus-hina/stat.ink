<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\PasskeyLoginAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 */

PasskeyLoginAsset::register($this);

$this->registerJs(sprintf(
    'window.__passkeyLoginConfig = %s;',
    Json::encode([
        'urls' => [
            'start' => Url::to(['user/passkey-login-start']),
            'finish' => Url::to(['user/passkey-login-finish']),
            'redirect' => Url::to(['user/profile']),
        ],
        'csrfParam' => Yii::$app->request->csrfParam,
        'csrfToken' => Yii::$app->request->csrfToken,
        'messages' => [
            'loginFailed' => Yii::t('app-passkey', 'Failed to log in with passkey.'),
        ],
    ]),
), View::POS_HEAD);
?>
<div class="panel panel-default mb-3" id="passkey-login-panel">
  <div class="panel-heading">
    <h2 class="panel-title">
      <?= Html::encode(Yii::t('app-passkey', 'Log in with Passkey')) . "\n" ?>
    </h2>
  </div>
  <div class="panel-body pb-0">
    <div class="form-group mb-3">
      <div class="checkbox">
        <label>
          <?= Html::checkbox('remember_me', true, ['id' => 'passkey-login-remember']) ?>
          <?= Html::encode(Yii::t('app', 'Remember me')) . "\n" ?>
        </label>
      </div>
    </div>
    <div class="form-group mb-3">
      <?= Html::tag(
        'button',
        Html::encode(Yii::t('app-passkey', 'Log in with Passkey')),
        [
          'type' => 'button',
          'id' => 'passkey-login-button',
          'class' => 'btn btn-primary btn-block',
        ],
      ) . "\n" ?>
    </div>
    <div id="passkey-login-message" class="mb-3" style="display:none"></div>
  </div>
</div>

<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\assets\PasskeyReauthAsset;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var View $this
 */

PasskeyReauthAsset::register($this);

$this->registerJs(sprintf(
  'window.__passkeyReauthConfig = %s;',
  Json::encode([
    'urls' => [
      'start' => Url::to(['user/passkey-reauth-start']),
      'finish' => Url::to(['user/passkey-reauth-finish']),
    ],
    'csrfParam' => Yii::$app->request->csrfParam,
    'csrfToken' => Yii::$app->request->csrfToken,
    'messages' => [
      'unsupported' => Yii::t('app-passkey', 'Your browser does not support passkeys.'),
      'failed' => Yii::t('app-passkey', 'Failed to verify with your passkey.'),
    ],
  ]),
), View::POS_HEAD);
?>
<div id="passkey-reauth-message" role="alert" style="display:none"></div>

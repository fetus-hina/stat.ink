<?php

declare(strict_types=1);

use app\components\helpers\IPHelper;
use app\components\helpers\UserAgentHelper;

$req = Yii::$app->request;
$t = function (
  string $message,
  array $params = [],
  string $category = 'app-email'
) use ($lang): string {
  return Yii::t($category, $message, $params, $lang);
};

echo implode("\n", [
  $t('Here is email verification code.'),
  $t('Please input this code to the browser. The code will be expired in {mins} minutes.', [
    'mins' => '60',
  ]),
  '',
  sprintf('%s %s', $t('Verification Code:'), $code),
  '',
  sprintf('%s %s', $t('IP Address:'), $req->userIP),
  sprintf('%s %s', $t('Rev. lookup:'), IPHelper::reverseLookup($req->userIP) ?: $t('(Failed)')),
  vsprintf('%s %s', [
    $t('Estimated location:'),
    IPHelper::getLocationByIP($req->userIP, $lang) ?: $t('(Unknown)'),
  ]),
  vsprintf('%s %s', [
    $t('Terminal:'),
    UserAgentHelper::summary($req->userAgent) ?: $t('(Unknown)'),
  ]),
]) . "\n";

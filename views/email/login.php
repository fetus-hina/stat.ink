<?php
declare(strict_types=1);

use app\components\helpers\IPHelper;

$req = Yii::$app->request;
$lang = ($user->emailLang ? $user->emailLang->lang : null) ?? 'en-US';
$t = function (
  string $message,
  array $params = [],
  string $category = 'app-email'
) use ($lang): string {
  return Yii::t($category, $message, $params, $lang);
};

echo implode("\n", [
  $t('There is a login on {site}.', ['site' => Yii::$app->name]),
  '',
  sprintf('%s %s', $t('IP Address:'), $req->userIP),
  sprintf('%s %s', $t('Rev. lookup:'), IPHelper::reverseLookup($req->userIP) ?: $t('(Failed)')),
  vsprintf('%s %s', [
    $t('Estimated location:'),
    IPHelper::getLocationByIP($req->userIP, $lang) ?: $t('(Unknown)'),
  ]),
  sprintf('%s %s', $t('Login method:'), $method ? $t($method->name) : $t('(Unknown)')),
]) . "\n";

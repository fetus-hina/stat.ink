<?php
declare(strict_types=1);

use app\components\helpers\IPHelper;

$req = Yii::$app->request;
$t = function (
  string $message,
  array $params = [],
  string $category = 'app-email'
) use ($lang): string {
  return Yii::t($category, $message, $params, $lang);
};

echo implode("\n", [
  $t('Your email address has been changed.'),
  '',
  sprintf('%s %s', $t('Old:'), $old ? $old : $t('(Empty)')),
  sprintf('%s %s', $t('New:'), $new ? $new : $t('(Empty)')),
  '',
  sprintf('%s %s', $t('IP Address:'), $req->userIP),
  sprintf('%s %s', $t('Rev. lookup:'), IPHelper::reverseLookup($req->userIP) ?: $t('(Failed)')),
  vsprintf('%s %s', [
    $t('Estimated location:'),
    IPHelper::getLocationByIP($req->userIP, $lang) ?: $t('(Unknown)'),
  ]),
]) . "\n";

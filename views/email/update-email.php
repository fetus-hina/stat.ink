<?php

declare(strict_types=1);

use app\components\helpers\IPHelper;
use app\components\helpers\UserAgentHelper;
use app\models\User;

/**
 * @var User $user
 * @var string $lang
 * @var string $new
 * @var string $old
 */

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
  vsprintf('%s %s', [
    $t('Terminal:'),
    UserAgentHelper::summary($req->userAgent) ?: $t('(Unknown)'),
  ]),
]) . "\n";

<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\web\User;
use app\models\LoginMethod;
use app\models\User as UserModel;
use app\models\UserLoginHistory;
use yii\web\Cookie;
use yii\web\ServerErrorHttpException;
use yii\web\UserEvent;

return (function (): array {
    $authKeyFile = dirname(__DIR__) . '/authkey-secret.php';
    $authKeySecret = @file_exists($authKeyFile)
        ? require($authKeyFile)
        : null;

    return [
        'class' => User::class,
        'autoRenewCookie' => false,
        'enableAutoLogin' => $authKeySecret !== null,
        'identityClass' => UserModel::class,
        'identityCookie' => [
            'httpOnly' => true,
            'name' => YII_ENV_DEV ? '_identity_dev' : '_identity',
            'sameSite' => Cookie::SAME_SITE_LAX,
            'secure' => (bool)preg_match(
                '/(?:^|\.)stat\.ink$/i',
                $_SERVER['HTTP_HOST'] ?? '',
            ),
        ],
        'identityFixedKey' => $authKeySecret,
        'loginUrl' => ['user/login'],
        'on afterLogin' => function (UserEvent $event): void {
            if (!$event->cookieBased) {
                // 通常ログインはこのフックでは記録しない。
                // 適切な箇所で UserLoginHistory::login() を呼び出すこと。
                return;
            }

            $identity = Yii::$app->user->getIdentity();
            if (!$identity) {
                // なんでやねん
                throw new ServerErrorHttpException('Internal error while auto-login process');
            }

            if (!headers_sent()) {
                Yii::$app->session->regenerateID(true);
            }

            UserLoginHistory::login($identity, LoginMethod::METHOD_COOKIE);
            UserModel::onLogin($identity, LoginMethod::METHOD_COOKIE);
        },
    ];
})();

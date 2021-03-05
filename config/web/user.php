<?php

declare(strict_types=1);

use app\components\web\User;
use app\models\LoginMethod;
use app\models\User as UserModel;
use app\models\UserLoginHistory;
use yii\web\ServerErrorHttpException;
use yii\web\UserEvent;

return (function (): array {
    $authKeyFile = dirname(__DIR__) . '/authkey-secret.php';
    $authKeySecret = @file_exists($authKeyFile)
        ? require($authKeyFile)
        : null;

    return [
        'class' => User::class,
        'identityFixedKey' => $authKeySecret,
        'identityClass' => UserModel::class,
        'identityCookie' => [
            'name' => '_identity',
            'httpOnly' => true,
            'secure' => (bool)preg_match(
                '/(?:^|\.)stat\.ink$/i',
                $_SERVER['HTTP_HOST'] ?? ''
            ),
        ],
        'enableAutoLogin' => $authKeySecret !== null,
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

            UserLoginHistory::login($identity, LoginMethod::METHOD_COOKIE);
            UserModel::onLogin($identity, LoginMethod::METHOD_COOKIE);
        },
    ];
})();

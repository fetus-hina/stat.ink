<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Service\ServiceInterface as OAuthService;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\OAuth2\Service\Google as GoogleService;
use OAuth\ServiceFactory as OAuthFactory;
use Throwable;
use Yii;
use app\models\LoginWithGoogle;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

final class LoginWithGoogleAction extends Action
{
    public function init()
    {
        if (!Yii::$app->params['google']['read_enabled']) {
            throw new BadRequestHttpException();
        }
    }

    public function run()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $google = $this->googleService;

        try {
            if ($request->get('error')) {
                // キャンセル等
                return $response->redirect(Url::to(['user/login'], true), 303);
            } elseif ($request->get('code')) {
                // 帰ってきた (state は lusitanian が内部で検証する)
                $google->requestAccessToken(
                    (string)$request->get('code'),
                    (string)$request->get('state'),
                );
                $userinfo = Json::decode(
                    $google->request('https://www.googleapis.com/oauth2/v3/userinfo'),
                );
                if (isset($userinfo['sub'])) {
                    $info = LoginWithGoogle::findOne(['google_id' => $userinfo['sub']]);
                    if ($info && $info->login()) {
                        return $this->controller->goBack(
                            ['show-user/profile',
                                'screen_name' => Yii::$app->user->identity->screen_name,
                            ],
                        );
                    }
                }

                Yii::$app->session->addFlash(
                    'danger',
                    Yii::t('app', 'There is no user associated with the specified Google account.'),
                );
                return $response->redirect(Url::to(['user/login'], true), 303);
            } else {
                // 認証手続き (state は lusitanian が自動生成・保存する)
                $url = $google->getAuthorizationUri();
                return $response->redirect((string)$url, 303);
            }
        } catch (Throwable $e) {
            throw $e;
        }
        throw new BadRequestHttpException('Bad request.');
    }

    public function getGoogleService(): OAuthService
    {
        $credential = new OAuthCredentials(
            Yii::$app->params['google']['client_id'],
            Yii::$app->params['google']['client_secret'],
            Url::to(['user/login-with-google'], true),
        );

        $factory = new OAuthFactory();
        return $factory->createService(
            'google',
            $credential,
            $this->tokenStorage,
            [GoogleService::SCOPE_EMAIL, GoogleService::SCOPE_PROFILE],
        );
    }

    public function getTokenStorage(): OAuthStorage
    {
        return new OAuthSessionStorage();
    }
}

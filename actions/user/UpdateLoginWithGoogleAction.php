<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Exception;
use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Service\ServiceInterface as OAuthService;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\OAuth2\Service\Google as GoogleService;
use OAuth\ServiceFactory as OAuthFactory;
use RuntimeException;
use Throwable;
use Yii;
use app\models\LoginWithGoogle;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use function is_array;

final class UpdateLoginWithGoogleAction extends Action
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
                return $response->redirect(Url::to(['user/profile'], true), 303);
            } elseif ($request->get('code')) {
                // 帰ってきた
                $google->requestAccessToken(
                    (string)$request->get('code'),
                    (string)$request->get('state'),
                );
                $gUser = Json::decode(
                    $google->request('https://www.googleapis.com/oauth2/v3/userinfo'),
                );
                if (!is_array($gUser) || !isset($gUser['sub'])) {
                    throw new RuntimeException('Failed to fetch your information from Google');
                }
                $user = Yii::$app->user->identity;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // 古い情報を探して古い情報があれば消す
                    $info = $user->loginWithGoogle;
                    if ($info) {
                        if (!$info->delete()) {
                            throw new Exception();
                        }
                    }

                    // 同じ google id の子がいないか確認する
                    $dupInfo = LoginWithGoogle::findOne(['google_id' => $gUser['sub']]);
                    if ($dupInfo) {
                        Yii::$app->session->addFlash(
                            'danger',
                            Yii::t('app', 'This Google account has already been integrated with another user.'),
                        );
                        $transaction->rollback();
                        return $response->redirect(Url::to(['user/profile'], true), 303);
                    }

                    // 新しい情報を登録する
                    $info = Yii::createObject([
                        'class' => LoginWithGoogle::class,
                        'user_id' => $user->id,
                        'google_id' => $gUser['sub'],
                        'email' => $gUser['email'] ?? null,
                        'name' => $gUser['name'] ?? ($gUser['email'] ?? $gUser['sub']),
                    ]);
                    if (!$info->save()) {
                        throw new Exception();
                    }
                    $transaction->commit();
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                } catch (Throwable $e) {
                    $transaction->rollback();
                    Yii::$app->session->addFlash(
                        'warning',
                        Yii::t('app', 'Please try again later.'),
                    );
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                }
            } else {
                // 認証手続き
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
            Url::to(['user/update-login-with-google'], true),
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

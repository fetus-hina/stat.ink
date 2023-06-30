<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Http\Uri\Uri as OAuthUri;
use OAuth\Common\Service\ServiceInterface as OAuthService;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\ServiceFactory as OAuthFactory;
use Throwable;
use Yii;
use app\models\LoginWithTwitter;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

final class LoginWithTwitterAction extends Action
{
    public function init()
    {
        if (!Yii::$app->params['twitter']['read_enabled']) {
            throw new BadRequestHttpException();
        }
    }

    public function run()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $twitter = $this->twitterService;

        try {
            if ($request->get('denied')) {
                // キャンセルしてきた
                return $response->redirect(Url::to(['user/login'], true), 303);
            } elseif ($request->get('oauth_token')) {
                // 帰ってきた
                $token = $this->tokenStorage->retrieveAccessToken('Twitter');
                $twitter->requestAccessToken(
                    (string)$request->get('oauth_token'),
                    (string)$request->get('oauth_verifier'),
                    $token->getRequestTokenSecret(),
                );
                $user = Json::decode(
                    $twitter->request('users/me'),
                );
                if (isset($user['data']['id'])) {
                    $info = LoginWithTwitter::findOne(['twitter_id' => $user['data']['id']]);
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
                    Yii::t('app', 'There is no user associated with the specified twitter account.'),
                );
                return $response->redirect(Url::to(['user/login'], true), 303);
            } else {
                // 認証手続き
                $token = $twitter->requestRequestToken();
                $url = $twitter->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
                return $response->redirect((string)$url, 303);
            }
        } catch (Throwable $e) {
            throw $e;
        }
        throw new BadRequestHttpException('Bad request.');
    }

    public function getTwitterService(): OAuthService
    {
        $credential = new OAuthCredentials(
            Yii::$app->params['twitter']['consumer_key'],
            Yii::$app->params['twitter']['consumer_secret'],
            Url::to(['user/login-with-twitter'], true),
        );

        $factory = new OAuthFactory();
        return $factory->createService(
            'twitter',
            $credential,
            $this->tokenStorage,
            baseApiUri: new OAuthUri('https://api.twitter.com/2/'),
        );
    }

    public function getTokenStorage(): OAuthStorage
    {
        return new OAuthSessionStorage();
    }
}

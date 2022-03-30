<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\OAuth1\Service\Twitter as TwitterService;
use OAuth\OAuth1\Token\TokenInterface as OAuthToken;
use OAuth\ServiceFactory as OAuthFactory;
use Throwable;
use Yii;
use app\components\helpers\T;
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
        $twitter = $this->getTwitterService();

        try {
            if ($request->get('denied')) {
                // キャンセルしてきた
                return $response->redirect(Url::to(['user/login'], true), 303);
            } elseif ($request->get('oauth_token')) {
                // 帰ってきた
                $token = T::is(
                    OAuthToken::class,
                    $this->getTokenStorage()->retrieveAccessToken('Twitter'),
                );
                $twitter->requestAccessToken(
                    (string)$request->get('oauth_token'),
                    (string)$request->get('oauth_verifier'),
                    $token->getRequestTokenSecret()
                );
                $user = Json::decode(
                    $twitter->request('account/verify_credentials.json')
                );

                $info = LoginWithTwitter::findOne(['twitter_id' => $user['id_str']]);
                if ($info && $info->login()) {
                    return T::webController($this->controller)
                        ->goBack(['show-user/profile',
                            'screen_name' => Yii::$app->user->identity->screen_name,
                        ]);
                }

                Yii::$app->session->addFlash(
                    'danger',
                    Yii::t('app', 'There is no user associated with the specified twitter account.')
                );
                return $response->redirect(Url::to(['user/login'], true), 303);
            } else {
                // 認証手続き
                $token = T::is(OAuthToken::class, $twitter->requestRequestToken());
                $url = $twitter->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
                return $response->redirect((string)$url, 303);
            }
        } catch (Throwable $e) {
        }
        throw new BadRequestHttpException('Bad request.');
    }

    public function getTwitterService(): TwitterService
    {
        $credential = new OAuthCredentials(
            Yii::$app->params['twitter']['consumer_key'],
            Yii::$app->params['twitter']['consumer_secret'],
            Url::to(['user/login-with-twitter'], true)
        );

        $factory = new OAuthFactory();
        return T::is(
            TwitterService::class,
            $factory->createService(
                'twitter',
                $credential,
                $this->getTokenStorage(),
            ),
        );
    }

    public function getTokenStorage(): OAuthStorage
    {
        return new OAuthSessionStorage();
    }
}

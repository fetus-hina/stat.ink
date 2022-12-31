<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Service\ServiceInterface as OAuthService;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\ServiceFactory as OAuthFactory;
use Yii;
use app\models\LoginWithTwitter;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\ViewAction as BaseAction;

class UpdateLoginWithTwitterAction extends BaseAction
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
                return $response->redirect(Url::to(['user/profile'], true), 303);
            } elseif ($request->get('oauth_token')) {
                // 帰ってきた
                $token = $this->tokenStorage->retrieveAccessToken('Twitter');
                $twitter->requestAccessToken(
                    (string)$request->get('oauth_token'),
                    (string)$request->get('oauth_verifier'),
                    $token->getRequestTokenSecret(),
                );
                $twUser = Json::decode(
                    $twitter->request('account/verify_credentials.json'),
                );
                $user = Yii::$app->user->identity;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // 古い情報を探して古い情報があれば消す
                    $info = $user->loginWithTwitter;
                    if ($info) {
                        if (!$info->delete()) {
                            throw new \Exception();
                        }
                    }

                    // 同じ twitter id の子がいないか確認する
                    $dupInfo = LoginWithTwitter::findOne(['twitter_id' => $twUser['id_str']]);
                    if ($dupInfo) {
                        Yii::$app->session->addFlash(
                            'danger',
                            Yii::t('app', 'This twitter account has already been integrated with another user.'),
                        );
                        $transaction->rollback();
                        return $response->redirect(Url::to(['user/profile'], true), 303);
                    }

                    // 新しい情報を登録する
                    $info = Yii::createObject([
                        'class' => LoginWithTwitter::class,
                        'user_id' => $user->id,
                        'twitter_id' => $twUser['id_str'],
                        'screen_name' => $twUser['screen_name'],
                        'name' => $twUser['name'],
                    ]);
                    if (!$info->save()) {
                        throw new \Exception();
                    }
                    $transaction->commit();
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                } catch (\Throwable $e) {
                    $transaction->rollback();
                    Yii::$app->session->addFlash(
                        'warning',
                        Yii::t('app', 'Please try again later.'),
                    );
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                }
            } else {
                // 認証手続き
                $token = $twitter->requestRequestToken();
                $url = $twitter->getAuthorizationUri(array('oauth_token' => $token->getRequestToken()));
                return $response->redirect((string)$url, 303);
            }
        } catch (\Throwable $e) {
        }
        throw new BadRequestHttpException('Bad request.');
    }

    public function getTwitterService(): OAuthService
    {
        $credential = new OAuthCredentials(
            Yii::$app->params['twitter']['consumer_key'],
            Yii::$app->params['twitter']['consumer_secret'],
            Url::to(['user/update-login-with-twitter'], true),
        );

        $factory = new OAuthFactory();
        return $factory->createService(
            'twitter',
            $credential,
            $this->tokenStorage,
        );
    }

    public function getTokenStorage(): OAuthStorage
    {
        return new OAuthSessionStorage();
    }
}

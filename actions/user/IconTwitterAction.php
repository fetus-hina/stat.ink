<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\user;

use Exception;
use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Service\ServiceInterface as OAuthService;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\ServiceFactory as OAuthFactory;
use Throwable;
use Yii;
use app\models\UserIcon;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\ViewAction as BaseAction;

use function file_get_contents;
use function str_replace;

class IconTwitterAction extends BaseAction
{
    public function init()
    {
        throw new BadRequestHttpException();
        // if (!Yii::$app->params['twitter']['read_enabled']) {
        //     throw new BadRequestHttpException();
        // }
    }

    public function run()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $twitter = $this->twitterService;

        try {
            if ($request->get('denied')) {
                // キャンセルしてきた
                return $response->redirect(Url::to(['user/edit-icon'], true), 303);
            } elseif ($request->get('oauth_token')) {
                // 帰ってきた
                $token = $this->tokenStorage->retrieveAccessToken('Twitter');
                $twitter->requestAccessToken(
                    (string)$request->get('oauth_token'),
                    (string)$request->get('oauth_verifier'),
                    $token->getRequestTokenSecret(),
                );
                $user = Json::decode(
                    $twitter->request('account/verify_credentials.json'),
                );
                if ($url = $user['profile_image_url_https'] ?? null) {
                    $url = str_replace('_normal', '', $url);
                }
                try {
                    if (!$url) {
                        // 利用不可
                        throw new Exception('Could not get url');
                    }
                    if (!$binary = file_get_contents($url)) {
                        throw new Exception('Could not get binary');
                    }
                    $transaction = Yii::$app->db->beginTransaction();
                    if ($icon = UserIcon::findOne(['user_id' => Yii::$app->user->identity->id])) {
                        if (!$icon->delete()) {
                            throw new Exception('UserIcon::delete failed');
                        }
                    }
                    $icon = UserIcon::createNew(Yii::$app->user->identity->id, $binary);
                    if (!$icon->save()) {
                        throw new Exception('UserIcon::save failed');
                    }
                    $transaction->commit();
                    Yii::$app->session->addFlash(
                        'danger',
                        Yii::t('app', 'Your profile icon has been updated.'),
                    );
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                } catch (Throwable $e) {
                    Yii::$app->session->addFlash(
                        'danger',
                        Yii::t('app', 'Could not get your twitter icon at this time.'),
                    );
                    return $response->redirect(Url::to(['user/edit-icon'], true), 303);
                }
            } else {
                // 認証手続き
                $token = $twitter->requestRequestToken();
                $url = $twitter->getAuthorizationUri(['oauth_token' => $token->getRequestToken()]);
                return $response->redirect((string)$url, 303);
            }
        } catch (Throwable $e) {
        }
        throw new BadRequestHttpException('Bad request.');
    }

    public function getTwitterService(): OAuthService
    {
        $credential = new OAuthCredentials(
            Yii::$app->params['twitter']['consumer_key'],
            Yii::$app->params['twitter']['consumer_secret'],
            Url::to(['user/icon-twitter'], true),
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

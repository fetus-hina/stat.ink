<?php

/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\user;

use Exception;
use OAuth\Common\Consumer\Credentials as OAuthCredentials;
use OAuth\Common\Storage\Session as OAuthSessionStorage;
use OAuth\Common\Storage\TokenStorageInterface as OAuthStorage;
use OAuth\OAuth1\Service\Twitter as TwitterService;
use OAuth\OAuth1\Token\TokenInterface as OAuthToken;
use OAuth\ServiceFactory as OAuthFactory;
use Throwable;
use Yii;
use app\components\helpers\T;
use app\models\UserIcon;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use function file_get_contents;

final class IconTwitterAction extends Action
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
                return $response->redirect(Url::to(['user/edit-icon'], true), 303);
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
                        Yii::t('app', 'Your profile icon has been updated.')
                    );
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                } catch (\Throwable $e) {
                    Yii::$app->session->addFlash(
                        'danger',
                        Yii::t('app', 'Could not get your twitter icon at this time.')
                    );
                    return $response->redirect(Url::to(['user/edit-icon'], true), 303);
                }
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
            Url::to(['user/icon-twitter'], true)
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

<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Server\Twitter as TwitterServer;
use Override;
use RuntimeException;
use Throwable;
use Yii;
use app\models\LoginWithTwitter;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use function is_array;

final class UpdateLoginWithTwitterAction extends Action
{
    private const SESSION_KEY_TEMP_CREDS = 'oauth1tempcreds.twitter.update';
    private const TWITTER_V2_USERS_ME = 'https://api.twitter.com/2/users/me';

    #[Override]
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
        $session = Yii::$app->session;
        $twitter = $this->createServer();

        try {
            if ($request->get('denied')) {
                $session->remove(self::SESSION_KEY_TEMP_CREDS);
                return $response->redirect(Url::to(['user/profile'], true), 303);
            } elseif ($request->get('oauth_token')) {
                $tempCreds = $this->restoreTemporaryCredentials($session->get(self::SESSION_KEY_TEMP_CREDS));
                $session->remove(self::SESSION_KEY_TEMP_CREDS);
                if (!$tempCreds) {
                    throw new BadRequestHttpException('Missing OAuth temporary credentials.');
                }

                $tokenCredentials = $twitter->getTokenCredentials(
                    $tempCreds,
                    (string)$request->get('oauth_token'),
                    (string)$request->get('oauth_verifier'),
                );

                $twUser = $this->fetchTwitterV2User($twitter, $tokenCredentials);
                if (!is_array($twUser) || !isset($twUser['data']['id'])) {
                    throw new RuntimeException('Failed to fetch your information from Twitter');
                }
                $user = Yii::$app->user->identity;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    $info = $user->loginWithTwitter;
                    if ($info) {
                        if (!$info->delete()) {
                            throw new Exception();
                        }
                    }

                    $dupInfo = LoginWithTwitter::findOne(['twitter_id' => $twUser['data']['id']]);
                    if ($dupInfo) {
                        Yii::$app->session->addFlash(
                            'danger',
                            Yii::t('app', 'This twitter account has already been integrated with another user.'),
                        );
                        $transaction->rollback();
                        return $response->redirect(Url::to(['user/profile'], true), 303);
                    }

                    $info = Yii::createObject([
                        'class' => LoginWithTwitter::class,
                        'user_id' => $user->id,
                        'twitter_id' => $twUser['data']['id'],
                        'screen_name' => $twUser['data']['username'],
                        'name' => $twUser['data']['name'],
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
                $tempCreds = $twitter->getTemporaryCredentials();
                $session->set(self::SESSION_KEY_TEMP_CREDS, [
                    'identifier' => $tempCreds->getIdentifier(),
                    'secret' => $tempCreds->getSecret(),
                ]);
                $url = $twitter->getAuthorizationUrl($tempCreds);
                return $response->redirect($url, 303);
            }
        } catch (Throwable $e) {
            throw $e;
        }
    }

    private function createServer(): TwitterServer
    {
        return new TwitterServer([
            'identifier' => Yii::$app->params['twitter']['consumer_key'],
            'secret' => Yii::$app->params['twitter']['consumer_secret'],
            'callback_uri' => Url::to(['user/update-login-with-twitter'], true),
        ]);
    }

    private function restoreTemporaryCredentials(mixed $stored): ?TemporaryCredentials
    {
        if (!is_array($stored) || !isset($stored['identifier'], $stored['secret'])) {
            return null;
        }
        $tempCreds = new TemporaryCredentials();
        $tempCreds->setIdentifier((string)$stored['identifier']);
        $tempCreds->setSecret((string)$stored['secret']);
        return $tempCreds;
    }

    private function fetchTwitterV2User(TwitterServer $twitter, $tokenCredentials): array
    {
        $url = self::TWITTER_V2_USERS_ME;
        $headers = $twitter->getHeaders($tokenCredentials, 'GET', $url);
        $client = new GuzzleClient();
        $response = $client->get($url, ['headers' => $headers]);
        return (array)Json::decode((string)$response->getBody());
    }
}

<?php

/**
 * @copyright Copyright (C) 2016-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\user;

use GuzzleHttp\Client as GuzzleClient;
use League\OAuth1\Client\Credentials\TemporaryCredentials;
use League\OAuth1\Client\Server\Twitter as TwitterServer;
use Override;
use Throwable;
use Yii;
use app\models\LoginWithTwitter;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use function is_array;

final class LoginWithTwitterAction extends Action
{
    private const SESSION_KEY_TEMP_CREDS = 'oauth1tempcreds.twitter';
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
                return $response->redirect(Url::to(['user/login'], true), 303);
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

                $user = $this->fetchTwitterV2User($twitter, $tokenCredentials);
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
            'callback_uri' => Url::to(['user/login-with-twitter'], true),
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

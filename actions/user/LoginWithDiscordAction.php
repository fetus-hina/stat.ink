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
use OAuth\ServiceFactory as OAuthFactory;
use Throwable;
use Yii;
use app\components\oauth\Discord as DiscordService;
use app\models\LoginWithDiscord;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

final class LoginWithDiscordAction extends Action
{
    public function init()
    {
        if (!Yii::$app->params['discord']['read_enabled']) {
            throw new BadRequestHttpException();
        }
    }

    public function run()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $discord = $this->discordService;

        try {
            if ($request->get('error')) {
                // キャンセル等
                return $response->redirect(Url::to(['user/login'], true), 303);
            } elseif ($request->get('code')) {
                // 帰ってきた (state は lusitanian が内部で検証する)
                $discord->requestAccessToken(
                    (string)$request->get('code'),
                    (string)$request->get('state'),
                );
                $userinfo = Json::decode(
                    $discord->request('users/@me'),
                );
                if (isset($userinfo['id'])) {
                    $info = LoginWithDiscord::findOne(['discord_id' => $userinfo['id']]);
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
                    Yii::t('app', 'There is no user associated with the specified Discord account.'),
                );
                return $response->redirect(Url::to(['user/login'], true), 303);
            } else {
                // 認証手続き
                $url = $discord->getAuthorizationUri();
                return $response->redirect((string)$url, 303);
            }
        } catch (Throwable $e) {
            throw $e;
        }
        throw new BadRequestHttpException('Bad request.');
    }

    public function getDiscordService(): OAuthService
    {
        $credential = new OAuthCredentials(
            Yii::$app->params['discord']['client_id'],
            Yii::$app->params['discord']['client_secret'],
            Url::to(['user/login-with-discord'], true),
        );

        $factory = new OAuthFactory();
        $factory->registerService('discord', DiscordService::class);
        return $factory->createService(
            'discord',
            $credential,
            $this->tokenStorage,
            [DiscordService::SCOPE_IDENTIFY, DiscordService::SCOPE_EMAIL],
        );
    }

    public function getTokenStorage(): OAuthStorage
    {
        return new OAuthSessionStorage();
    }
}

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
use OAuth\ServiceFactory as OAuthFactory;
use RuntimeException;
use Throwable;
use Yii;
use app\components\oauth\Discord as DiscordService;
use app\models\LoginWithDiscord;
use yii\base\Action;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

use function is_array;

final class UpdateLoginWithDiscordAction extends Action
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
                return $response->redirect(Url::to(['user/profile'], true), 303);
            } elseif ($request->get('code')) {
                // 帰ってきた
                $discord->requestAccessToken(
                    (string)$request->get('code'),
                    (string)$request->get('state'),
                );
                $dUser = Json::decode(
                    $discord->request('users/@me'),
                );
                if (!is_array($dUser) || !isset($dUser['id'])) {
                    throw new RuntimeException('Failed to fetch your information from Discord');
                }
                $user = Yii::$app->user->identity;

                $transaction = Yii::$app->db->beginTransaction();
                try {
                    // 古い情報を探して古い情報があれば消す
                    $info = $user->loginWithDiscord;
                    if ($info) {
                        if (!$info->delete()) {
                            throw new Exception();
                        }
                    }

                    // 同じ discord id の子がいないか確認する
                    $dupInfo = LoginWithDiscord::findOne(['discord_id' => $dUser['id']]);
                    if ($dupInfo) {
                        Yii::$app->session->addFlash(
                            'danger',
                            Yii::t('app', 'This Discord account has already been integrated with another user.'),
                        );
                        $transaction->rollback();
                        return $response->redirect(Url::to(['user/profile'], true), 303);
                    }

                    // 新しい情報を登録する
                    $info = Yii::createObject([
                        'class' => LoginWithDiscord::class,
                        'user_id' => $user->id,
                        'discord_id' => $dUser['id'],
                        'email' => $dUser['email'] ?? null,
                        'name' => $dUser['global_name']
                            ?? $dUser['username']
                            ?? ($dUser['email'] ?? $dUser['id']),
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
            Url::to(['user/update-login-with-discord'], true),
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

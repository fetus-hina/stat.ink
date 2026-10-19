<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user\oauth2;

use League\OAuth2\Client\Provider\AbstractProvider;
use Override;
use Yii;
use app\components\helpers\TypeHelper;
use app\models\OAuth2LoginLinkInterface;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Controller;

abstract class AbstractOAuth2LoginAction extends Action
{
    abstract protected function isProviderEnabled(): bool;

    abstract protected function createProvider(): AbstractProvider;

    abstract protected function getSessionKeyState(): string;

    /**
     * Extra options passed to AbstractProvider::getAuthorizationUrl(),
     * e.g. ['scope' => ['identify', 'email']]. Return [] to use provider defaults.
     */
    abstract protected function getAuthorizationOptions(): array;

    /**
     * Locate the LoginWithXxx record for the given
     * external account id. Returning null means "no user is associated".
     */
    abstract protected function findExistingLink(int|string $externalId): ?OAuth2LoginLinkInterface;

    abstract protected function getNoUserFoundMessage(): string;

    #[Override]
    public function init()
    {
        if (!$this->isProviderEnabled()) {
            throw new BadRequestHttpException();
        }
    }

    public function run()
    {
        $request = Yii::$app->request;
        $response = Yii::$app->response;
        $session = Yii::$app->session;
        $provider = $this->createProvider();
        $sessionKey = $this->getSessionKeyState();

        if ($request->get('error')) {
            $session->remove($sessionKey);
            return $response->redirect(Url::to(['user/login'], true), 303);
        }

        if ($request->get('code')) {
            $expectedState = $session->get($sessionKey);
            $session->remove($sessionKey);
            $actualState = (string)$request->get('state');
            if (!$expectedState || $actualState !== (string)$expectedState) {
                throw new BadRequestHttpException('Invalid state parameter.');
            }

            $token = $provider->getAccessToken('authorization_code', [
                'code' => (string)$request->get('code'),
            ]);
            $resourceOwner = $provider->getResourceOwner($token);
            $externalId = $resourceOwner->getId();
            if ($externalId !== null && $externalId !== '') {
                $info = $this->findExistingLink($externalId);
                if ($info && $info->login()) {
                    return TypeHelper::instanceOf($this->controller, Controller::class)->goBack(
                        ['show-user/profile',
                            'screen_name' => Yii::$app->user->identity->screen_name,
                        ],
                    );
                }
            }

            $session->addFlash('danger', $this->getNoUserFoundMessage());
            return $response->redirect(Url::to(['user/login'], true), 303);
        }

        $url = $provider->getAuthorizationUrl($this->getAuthorizationOptions());
        $session->set($sessionKey, $provider->getState());
        return $response->redirect($url, 303);
    }
}

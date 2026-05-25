<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user\oauth2;

use Exception;
use League\OAuth2\Client\Provider\AbstractProvider;
use Override;
use RuntimeException;
use Throwable;
use Yii;
use app\models\User;
use yii\base\Action;
use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;

abstract class AbstractOAuth2UpdateLoginAction extends Action
{
    abstract protected function isProviderEnabled(): bool;

    abstract protected function createProvider(): AbstractProvider;

    abstract protected function getSessionKeyState(): string;

    /**
     * Extra options passed to AbstractProvider::getAuthorizationUrl().
     */
    abstract protected function getAuthorizationOptions(): array;

    /**
     * Return the currently linked LoginWithXxx for $user, or null. Typically the relation
     * getter (e.g. $user->loginWithGoogle).
     */
    abstract protected function getCurrentUserLink(User $user): ?ActiveRecord;

    /**
     * Return a LoginWithXxx already linked to ANY user by the given external id, or null.
     * Used to detect that the account is already taken.
     */
    abstract protected function findDuplicateLink(int|string $externalId): ?ActiveRecord;

    /**
     * Build (but do not save) a new LoginWithXxx for $user using $userData (the raw
     * resource owner response) and the resolved $externalId.
     */
    abstract protected function createNewLink(
        User $user,
        int|string $externalId,
        array $userData,
    ): ActiveRecord;

    abstract protected function getAlreadyIntegratedMessage(): string;

    abstract protected function getFailedToFetchMessage(): string;

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
            return $response->redirect(Url::to(['user/profile'], true), 303);
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
            if ($externalId === null || $externalId === '') {
                throw new RuntimeException($this->getFailedToFetchMessage());
            }
            $userData = $resourceOwner->toArray();
            $user = Yii::$app->user->identity;

            $transaction = Yii::$app->db->beginTransaction();
            try {
                $existing = $this->getCurrentUserLink($user);
                if ($existing) {
                    if (!$existing->delete()) {
                        throw new Exception();
                    }
                }

                $duplicate = $this->findDuplicateLink($externalId);
                if ($duplicate) {
                    $session->addFlash('danger', $this->getAlreadyIntegratedMessage());
                    $transaction->rollback();
                    return $response->redirect(Url::to(['user/profile'], true), 303);
                }

                $newLink = $this->createNewLink($user, $externalId, $userData);
                if (!$newLink->save()) {
                    throw new Exception();
                }
                $transaction->commit();
                return $response->redirect(Url::to(['user/profile'], true), 303);
            } catch (Throwable $e) {
                $transaction->rollback();
                $session->addFlash('warning', Yii::t('app', 'Please try again later.'));
                return $response->redirect(Url::to(['user/profile'], true), 303);
            }
        }

        $url = $provider->getAuthorizationUrl($this->getAuthorizationOptions());
        $session->set($sessionKey, $provider->getState());
        return $response->redirect($url, 303);
    }
}

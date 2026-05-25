<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user\oauth2;

use League\OAuth2\Client\Provider\Google as GoogleProvider;
use Override;
use Yii;
use app\models\LoginWithGoogle;
use app\models\User;
use yii\helpers\Url;

final class UpdateLoginWithGoogleAction extends AbstractOAuth2UpdateLoginAction
{
    #[Override]
    protected function isProviderEnabled(): bool
    {
        return (bool)Yii::$app->params['google']['read_enabled'];
    }

    #[Override]
    protected function createProvider(): GoogleProvider
    {
        return new GoogleProvider([
            'clientId' => Yii::$app->params['google']['client_id'],
            'clientSecret' => Yii::$app->params['google']['client_secret'],
            'redirectUri' => Url::to(['user/update-login-with-google'], true),
        ]);
    }

    #[Override]
    protected function getSessionKeyState(): string
    {
        return 'oauth2state.google.update';
    }

    #[Override]
    protected function getAuthorizationOptions(): array
    {
        return [];
    }

    #[Override]
    protected function getCurrentUserLink(User $user): ?LoginWithGoogle
    {
        return $user->loginWithGoogle;
    }

    #[Override]
    protected function findDuplicateLink(int|string $externalId): ?LoginWithGoogle
    {
        return LoginWithGoogle::findOne(['google_id' => $externalId]);
    }

    #[Override]
    protected function createNewLink(User $user, int|string $externalId, array $userData): LoginWithGoogle
    {
        return Yii::createObject([
            'class' => LoginWithGoogle::class,
            'user_id' => $user->id,
            'google_id' => $externalId,
            'email' => $userData['email'] ?? null,
            'name' => $userData['name'] ?? ($userData['email'] ?? (string)$externalId),
        ]);
    }

    #[Override]
    protected function getAlreadyIntegratedMessage(): string
    {
        return Yii::t('app', 'This Google account has already been integrated with another user.');
    }

    #[Override]
    protected function getFailedToFetchMessage(): string
    {
        return 'Failed to fetch your information from Google';
    }
}

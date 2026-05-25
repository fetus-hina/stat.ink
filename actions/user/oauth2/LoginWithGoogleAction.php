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
use yii\helpers\Url;

final class LoginWithGoogleAction extends AbstractOAuth2LoginAction
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
            'redirectUri' => Url::to(['user/login-with-google'], true),
        ]);
    }

    #[Override]
    protected function getSessionKeyState(): string
    {
        return 'oauth2state.google';
    }

    #[Override]
    protected function getAuthorizationOptions(): array
    {
        return [];
    }

    #[Override]
    protected function findExistingLink(int|string $externalId): ?LoginWithGoogle
    {
        return LoginWithGoogle::findOne(['google_id' => $externalId]);
    }

    #[Override]
    protected function getNoUserFoundMessage(): string
    {
        return Yii::t('app', 'There is no user associated with the specified Google account.');
    }
}

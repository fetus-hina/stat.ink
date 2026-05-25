<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\user\oauth2;

use Override;
use Wohali\OAuth2\Client\Provider\Discord as DiscordProvider;
use Yii;
use app\models\LoginWithDiscord;
use yii\helpers\Url;

final class LoginWithDiscordAction extends AbstractOAuth2LoginAction
{
    #[Override]
    protected function isProviderEnabled(): bool
    {
        return (bool)Yii::$app->params['discord']['read_enabled'];
    }

    #[Override]
    protected function createProvider(): DiscordProvider
    {
        return new DiscordProvider([
            'clientId' => Yii::$app->params['discord']['client_id'],
            'clientSecret' => Yii::$app->params['discord']['client_secret'],
            'redirectUri' => Url::to(['user/login-with-discord'], true),
        ]);
    }

    #[Override]
    protected function getSessionKeyState(): string
    {
        return 'oauth2state.discord';
    }

    #[Override]
    protected function getAuthorizationOptions(): array
    {
        return ['scope' => ['identify', 'email']];
    }

    #[Override]
    protected function findExistingLink(int|string $externalId): ?LoginWithDiscord
    {
        return LoginWithDiscord::findOne(['discord_id' => $externalId]);
    }

    #[Override]
    protected function getNoUserFoundMessage(): string
    {
        return Yii::t('app', 'There is no user associated with the specified Discord account.');
    }
}

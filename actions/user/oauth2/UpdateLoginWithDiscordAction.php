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
use app\models\User;
use yii\helpers\Url;

final class UpdateLoginWithDiscordAction extends AbstractOAuth2UpdateLoginAction
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
            'redirectUri' => Url::to(['user/update-login-with-discord'], true),
        ]);
    }

    #[Override]
    protected function getSessionKeyState(): string
    {
        return 'oauth2state.discord.update';
    }

    #[Override]
    protected function getAuthorizationOptions(): array
    {
        return ['scope' => ['identify', 'email']];
    }

    #[Override]
    protected function getCurrentUserLink(User $user): ?LoginWithDiscord
    {
        return $user->loginWithDiscord;
    }

    #[Override]
    protected function findDuplicateLink(int|string $externalId): ?LoginWithDiscord
    {
        return LoginWithDiscord::findOne(['discord_id' => $externalId]);
    }

    #[Override]
    protected function createNewLink(User $user, int|string $externalId, array $userData): LoginWithDiscord
    {
        return Yii::createObject([
            'class' => LoginWithDiscord::class,
            'user_id' => $user->id,
            'discord_id' => $externalId,
            'email' => $userData['email'] ?? null,
            'name' => $userData['global_name']
                ?? $userData['username']
                ?? ($userData['email'] ?? (string)$externalId),
        ]);
    }

    #[Override]
    protected function getAlreadyIntegratedMessage(): string
    {
        return Yii::t('app', 'This Discord account has already been integrated with another user.');
    }

    #[Override]
    protected function getFailedToFetchMessage(): string
    {
        return 'Failed to fetch your information from Discord';
    }
}

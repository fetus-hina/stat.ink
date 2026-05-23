<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\oauth;

use OAuth\Common\Consumer\CredentialsInterface;
use OAuth\Common\Http\Client\ClientInterface;
use OAuth\Common\Http\Exception\TokenResponseException;
use OAuth\Common\Http\Uri\Uri;
use OAuth\Common\Http\Uri\UriInterface;
use OAuth\Common\Storage\TokenStorageInterface;
use OAuth\OAuth2\Service\AbstractService;
use OAuth\OAuth2\Token\StdOAuth2Token;

use function is_array;
use function json_decode;

/**
 * Discord OAuth 2.0 service for lusitanian/oauth.
 *
 * @see https://discord.com/developers/docs/topics/oauth2
 */
final class Discord extends AbstractService
{
    public const SCOPE_IDENTIFY = 'identify';
    public const SCOPE_EMAIL = 'email';

    public function __construct(
        CredentialsInterface $credentials,
        ClientInterface $httpClient,
        TokenStorageInterface $storage,
        $scopes = [],
        ?UriInterface $baseApiUri = null,
    ) {
        parent::__construct($credentials, $httpClient, $storage, $scopes, $baseApiUri, true);

        if ($baseApiUri === null) {
            $this->baseApiUri = new Uri('https://discord.com/api/');
        }
    }

    /**
     * @inheritdoc
     */
    public function getAuthorizationEndpoint()
    {
        return new Uri('https://discord.com/oauth2/authorize');
    }

    /**
     * @inheritdoc
     */
    public function getAccessTokenEndpoint()
    {
        return new Uri('https://discord.com/api/oauth2/token');
    }

    /**
     * @inheritdoc
     */
    protected function getAuthorizationMethod()
    {
        return static::AUTHORIZATION_METHOD_HEADER_BEARER;
    }

    /**
     * @inheritdoc
     */
    protected function parseAccessTokenResponse($responseBody)
    {
        $data = json_decode($responseBody, true);

        if (!is_array($data)) {
            throw new TokenResponseException('Unable to parse response.');
        }
        if (isset($data['error'])) {
            throw new TokenResponseException('Error in retrieving token: "' . $data['error'] . '"');
        }

        $token = new StdOAuth2Token();
        $token->setAccessToken($data['access_token']);
        if (isset($data['expires_in'])) {
            $token->setLifetime($data['expires_in']);
        }
        if (isset($data['refresh_token'])) {
            $token->setRefreshToken($data['refresh_token']);
            unset($data['refresh_token']);
        }

        unset($data['access_token'], $data['expires_in']);
        $token->setExtraParams($data);

        return $token;
    }
}

<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use ParagonIE\ConstantTime\Base64UrlSafe;
use Yii;
use jp3cki\uuid\Uuid;
use lbuchs\WebAuthn\WebAuthn;

use function random_bytes;

final class WebAuthnHelper
{
    public const SESSION_KEY_CHALLENGE = 'passkey.register.challenge';
    public const SESSION_KEY_LOGIN_CHALLENGE = 'passkey.login.challenge';

    public static function create(): WebAuthn
    {
        return new WebAuthn(
            rpName: Yii::$app->name,
            rpId: self::getRpId(),
            allowedFormats: ['none', 'packed', 'tpm', 'android-key', 'android-safetynet', 'apple', 'fido-u2f'],
            useBase64UrlEncoding: true,
        );
    }

    public static function getRpId(): string
    {
        return Yii::$app->request->hostName ?? 'stat.ink';
    }

    public static function generateUserHandleBase64(): string
    {
        return self::base64UrlEncode(random_bytes(64));
    }

    public static function base64UrlEncode(string $binary): string
    {
        return Base64UrlSafe::encodeUnpadded($binary);
    }

    public static function base64UrlDecode(string $encoded): string
    {
        return Base64UrlSafe::decode($encoded);
    }

    public static function binaryToUuidString(string $binary): string
    {
        return Uuid::fromString($binary)->formatAsString();
    }
}

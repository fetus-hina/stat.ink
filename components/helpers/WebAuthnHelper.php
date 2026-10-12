<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\helpers;

use InvalidArgumentException;
use ParagonIE\ConstantTime\Base64UrlSafe;
use Yii;
use lbuchs\WebAuthn\WebAuthn;

use function array_filter;
use function array_values;
use function bin2hex;
use function implode;
use function in_array;
use function is_array;
use function is_string;
use function random_bytes;
use function strlen;
use function substr;

final class WebAuthnHelper
{
    private const ALLOWED_TRANSPORTS = ['usb', 'nfc', 'ble', 'internal', 'hybrid', 'smart-card'];

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

    /**
     * Formats a 16-byte AAGUID as a UUID string
     *
     * An AAGUID is just 16 bytes and does not necessarily follow the RFC 9562
     * variant/version bits (e.g., Google Password Manager), so it is not
     * validated as a UUID.
     */
    public static function binaryToUuidString(string $binary): string
    {
        if (strlen($binary) !== 16) {
            throw new InvalidArgumentException('AAGUID must be exactly 16 bytes');
        }

        $hex = bin2hex($binary);
        return implode('-', [
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12),
        ]);
    }

    /**
     * Filters the client-reported transports (getTransports()) down to the known values
     *
     * @return list<string>
     */
    public static function normalizeTransports(mixed $transports): array
    {
        if (!is_array($transports)) {
            return [];
        }

        return array_values(array_filter(
            $transports,
            fn ($v): bool => is_string($v) && in_array($v, self::ALLOWED_TRANSPORTS, true),
        ));
    }
}

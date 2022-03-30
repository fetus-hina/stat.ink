<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\components\web;

use Yii;
use yii\base\InvalidValueException;
use yii\helpers\StringHelper;
use yii\web\Cookie;
use yii\web\IdentityInterface;
use yii\web\User as BaseUser;

use const JSON_ERROR_NONE;
use const OPENSSL_RAW_DATA;

class User extends BaseUser
{
    public const CRYPT_KEY_BITS = 256;
    public const CRYPT_METHOD = 'aes-256-gcm';
    public const CRYPT_KEY_SALT_BYTES = 16;

    public $identityFixedKey;

    protected function sendIdentityCookie($identity, $duration)
    {
        $cookie = Yii::createObject(array_merge($this->identityCookie, [
            'class' => Cookie::class,
            'value' => $this->createIdentityCookieValue($identity, $duration),
            'expire' => time() + $duration,
        ]));
        Yii::$app->getResponse()->getCookies()->add($cookie);
    }

    protected function getIdentityAndDurationFromCookie()
    {
        $value = Yii::$app->getRequest()
            ->getCookies()
            ->getValue($this->identityCookie['name']);
        if ($value === null) {
            return null;
        }

        $data = $this->decodeIdentityCookieValue($value);
        if ($data) {
            $class = $this->identityClass;
            $identity = $class::findIdentity($data['id']);
            if ($identity !== null) {
                if (!$identity instanceof IdentityInterface) {
                    throw new InvalidValueException(
                        "$class::findIdentity() must return an object " .
                        'implementing IdentityInterface.'
                    );
                } elseif (!$identity->validateAuthKey($data['authKey'])) {
                    Yii::warning(
                        sprintf(
                            "Invalid auth key attempted for user '%s': %s",
                            $data['id'],
                            $data['authKey']
                        ),
                        __METHOD__
                    );
                } else {
                    return [
                        'identity' => $identity,
                        'duration' => $data['duration'],
                    ];
                }
            }
        }

        $this->removeIdentityCookie();
        return null;
    }

    private function createIdentityCookieValue(
        IdentityInterface $identity,
        int $duration
    ): string {
        Yii::beginProfile('createIdentityCookieValue', __METHOD__);

        $security = Yii::$app->security;
        $ivBinary = $security->generateRandomKey(
            openssl_cipher_iv_length(static::CRYPT_METHOD)
        );
        $keySaltBinary = $security->generateRandomKey(static::CRYPT_KEY_SALT_BYTES);
        $keyBinary = $this->generateIdentityEncodeKey($keySaltBinary);
        $tagBinary = null;
        $rawData = json_encode([
            'id' => $identity->getId(),
            'authKey' => $identity->getAuthKey(),
            'duration' => $duration,
        ]);
        $cryptedData = openssl_encrypt(
            $rawData,
            static::CRYPT_METHOD,
            $keyBinary,
            OPENSSL_RAW_DATA,
            $ivBinary,
            $tagBinary,
            '',
            16
        );
        $result = StringHelper::base64UrlEncode(implode('', [
            $ivBinary,
            $keySaltBinary,
            $tagBinary,
            $cryptedData,
        ]));
        Yii::endProfile('createIdentityCookieValue', __METHOD__);
        return $result;
    }

    private function decodeIdentityCookieValue(string $value): ?array
    {
        Yii::beginProfile('decodeIdentityCookieValue', __METHOD__);
        try {
            $value = @StringHelper::base64UrlDecode($value);
            if (!$value) {
                return null;
            }

            $ivLen = openssl_cipher_iv_length(static::CRYPT_METHOD);
            $ivBinary = substr($value, 0, $ivLen);
            if (strlen($value) < $ivLen) {
                return null;
            }
            $value = substr($value, $ivLen);

            $keySaltBinary = substr($value, 0, static::CRYPT_KEY_SALT_BYTES);
            if (strlen($keySaltBinary) < static::CRYPT_KEY_SALT_BYTES) {
                return null;
            }
            $value = substr($value, static::CRYPT_KEY_SALT_BYTES);

            $tagBinary = substr($value, 0, 16);
            if (strlen($tagBinary) < 16) {
                return null;
            }

            $value = substr($value, 16);
            if (strlen($value) < 1) {
                return null;
            }

            $decoded = @openssl_decrypt(
                $value,
                static::CRYPT_METHOD,
                $this->generateIdentityEncodeKey($keySaltBinary),
                OPENSSL_RAW_DATA,
                $ivBinary,
                $tagBinary
            );
            if (!$decoded) {
                return null;
            }

            $json = json_decode($decoded, true);
            return json_last_error() === JSON_ERROR_NONE
                ? $json
                : null;
        } finally {
            Yii::endProfile('decodeIdentityCookieValue', __METHOD__);
        }
    }

    private function generateIdentityEncodeKey(string $saltBinary): string
    {
        Yii::beginProfile('generateIdentityEncodeKey', __METHOD__);
        $result = Yii::$app->security->pbkdf2(
            'sha512',
            $saltBinary,
            $this->identityFixedKey,
            1000,
            static::CRYPT_KEY_BITS / 8
        );
        Yii::endProfile('generateIdentityEncodeKey', __METHOD__);
        return $result;
    }
}

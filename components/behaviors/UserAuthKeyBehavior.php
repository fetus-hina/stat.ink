<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\helpers\StringHelper;

use const PASSWORD_ARGON2I;

class UserAuthKeyBehavior extends Behavior
{
    public const RAW_KEY_BITS = 256;

    public static function raw2hint(string $input): string
    {
        return hash('crc32b', $input, false);
    }

    public function events()
    {
        return [
            Model::EVENT_BEFORE_VALIDATE => 'fillAuthKeyAttributes',
        ];
    }

    public function fillAuthKeyAttributes(): void
    {
        // "raw", but Base64'ed
        $raw_key = $this->generateRawKey();

        $this->owner->auth_key_raw = $raw_key;

        // hint for finding
        $this->owner->auth_key_hint = static::raw2hint($raw_key);

        // hashed token
        $this->owner->auth_key_hash = password_hash(
            $raw_key,
            PASSWORD_ARGON2I //TODO: use PASSWORD_ARGON2ID after upgrade to PHP 7.3
        );
    }

    protected function generateRawKey(): string
    {
        $security = Yii::$app->security;
        $bytes = (int)ceil(static::RAW_KEY_BITS / 8);
        $binary = $security->generateRandomKey($bytes);
        return rtrim(StringHelper::base64UrlEncode($binary), '=');
    }
}

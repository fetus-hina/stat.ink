<?php

/**
 * @copyright Copyright (C) 2018-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\behaviors;

use LogicException;
use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\helpers\StringHelper;

use function ceil;
use function defined;
use function hash;
use function password_hash;
use function rtrim;

use const PASSWORD_ARGON2I;
use const PASSWORD_ARGON2ID;
use const PASSWORD_BCRYPT;

final class UserAuthKeyBehavior extends Behavior
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

        [$algo, $algoOptions] = $this->getHashOptions();

        // hashed token
        $this->owner->auth_key_hash = password_hash($raw_key, $algo, $algoOptions);
    }

    protected function generateRawKey(): string
    {
        $security = Yii::$app->security;
        $bytes = (int)ceil(static::RAW_KEY_BITS / 8);
        $binary = $security->generateRandomKey($bytes);
        return rtrim(StringHelper::base64UrlEncode($binary), '=');
    }

    /**
     * @return array{string, array<string, int>}
     */
    private function getHashOptions(): array
    {
        return match (true) {
            defined('PASSWORD_ARGON2ID'), defined('PASSWORD_ARGON2I') => [
                match (true) {
                    defined('PASSWORD_ARGON2ID') => PASSWORD_ARGON2ID,
                    defined('PASSWORD_ARGON2I') => PASSWORD_ARGON2I,
                    default => throw new LogicException(),
                },
                [
                    'memory_cost' => 1024,
                    'time_cost' => 2,
                    'threads' => 2,
                ],
            ],
            default => [PASSWORD_BCRYPT, []],
        };
    }
}

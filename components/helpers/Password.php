<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

class Password
{
    public static function hash($password)
    {
        return password_hash(self::preprocess($password), PASSWORD_DEFAULT);
    }

    public static function verify($password, $hash)
    {
        return password_verify(
            self::preprocess($password, self::detectAlgorithm($hash)),
            $hash
        );
    }

    public static function needsRehash($hash)
    {
        return password_needs_rehash($hash, PASSWORD_DEFAULT);
    }

    private static function preprocess($password, $algo = PASSWORD_DEFAULT)
    {
        if ($algo !== PASSWORD_BCRYPT) {
            return $password;
        }

        $hash = rtrim(base64_encode(hash('sha256', $password, true)), '=');
        return substr("{$hash}:{$password}", 0, 72);
    }

    private static function detectAlgorithm($hash)
    {
        if (substr($hash, 0, 4) === '$2y$') {
            return PASSWORD_BCRYPT;
        }
        // TODO
        return PASSWORD_DEFAULT;
    }
}

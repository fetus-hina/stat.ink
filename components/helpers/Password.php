<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use yii\helpers\ArrayHelper;

use const PASSWORD_ARGON2I;
use const PASSWORD_ARGON2ID;
use const PASSWORD_BCRYPT;
use const PASSWORD_DEFAULT;
use const PHP_VERSION;

class Password
{
    public static function hash(string $password): string
    {
        $algo = static::currentAlgo();
        return password_hash(
            self::preprocess($password, $algo),
            $algo
        );
    }

    public static function verify(
        string $password,
        string $hash
    ): bool {
        return password_verify(
            self::preprocess($password, self::detectAlgorithm($hash)),
            $hash
        );
    }

    public static function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, static::currentAlgo());
    }

    private static function preprocess(string $password, $algo): string
    {
        if ($algo !== PASSWORD_BCRYPT) {
            return $password;
        }

        $hash = rtrim(base64_encode(hash('sha256', $password, true)), '=');
        return substr("{$hash}:{$password}", 0, 72);
    }

    private static function detectAlgorithm(string $hash)
    {
        foreach (static::algoList() as $algoInfo) {
            foreach ($algoInfo['prefixes'] as $prefix) {
                if (substr($hash, 0, strlen($prefix)) === $prefix) {
                    return $algoInfo['algo'];
                }
            }
        }

        return PASSWORD_DEFAULT;
    }

    private static function currentAlgo()
    {
        // minimumPHP （最小バージョン実行環境）として定義された
        // バージョンで利用可能な最高のアルゴリズムを選択する
        // 7.1.x : PASSWORD_BCRYPT
        // 7.2.x : PASSWORD_ARGON2I
        // 7.3.x : PASSWORD_ARGON2ID
        // が選択されるはず
        $phpVersion = ArrayHelper::getValue(Yii::$app->params, 'minimumPHP', '7.1.0');
        foreach (static::algoList() as $algoInfo) {
            if (version_compare($phpVersion, $algoInfo['php'], '>=')) {
                return $algoInfo['algo'];
            }
        }
        return PASSWORD_DEFAULT;
    }

    private static function algoList(): array
    {
        // order: stronger to weaker
        return array_merge(
            version_compare(PHP_VERSION, '7.3.0', '>=') && defined('PASSWORD_ARGON2ID')
                ? [
                    [
                        'php' => '7.3.0',
                        'algo' => PASSWORD_ARGON2ID,
                        'prefixes' => [
                            '$argon2id$',
                        ],
                    ],
                ]
                : [],
            version_compare(PHP_VERSION, '7.2.0', '>=') && defined('PASSWORD_ARGON2I')
                ? [
                    [
                        'php' => '7.2.0',
                        'algo' => PASSWORD_ARGON2I,
                        'prefixes' => [
                            '$argon2i$',
                        ],
                    ],
                ]
                : [],
            [
                [
                    'php' => '5.5.0',
                    'algo' => PASSWORD_BCRYPT,
                    'prefixes' => [
                        '$2y$',
                        '$2x$',
                        '$2a$',
                        '$2b$',
                        '$2$',
                    ],
                ],
            ]
        );
    }
}

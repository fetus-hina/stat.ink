<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Base32\Base32;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

final class AssetHashHelper
{
    private const ALGO_VERSION = 2;

    public static function calc(string $path): string
    {
        $path = self::rebaseAppPath($path);
        $profile = Profiler::profile(sprintf('Calc asset hash (%s)', $path), __METHOD__);
        try {
            /** @var array<string, scalar>|null $options */
            static $options = null;
            if ($options === null) {
                $options = [
                    'algoVersion' => self::ALGO_VERSION,
                    'assetRevision' => (int)ArrayHelper::getValue(
                        Yii::$app->params,
                        'assetRevision',
                        -1,
                    ),
                ];
            }

            $commitDate = self::getLastCommitDate();

            $hash = self::calcImpl($path, $options);

            return $commitDate === null
                ? $hash
                : vsprintf('%s-%s/%s', [
                    $commitDate,
                    $options['assetRevision'] >= 0 ? (string)$options['assetRevision'] : '_',
                    $hash,
                ]);
        } finally {
            unset($profile);
        }
    }

    private static function rebaseAppPath(string $path): string
    {
        $appPath = dirname((string)Yii::getAlias('@webroot'));
        $path = (is_file($path) ? dirname($path) : $path);

        return strncmp($path, $appPath, strlen($appPath)) === 0
            ? '@app/' . ltrim(substr($path, strlen($appPath)), '/')
            : $path;
    }

    private static function calcImpl(string $path, array $options): string
    {
        return strtolower(
            substr(
                Base32::encode(
                    hash_hmac(
                        'sha256',
                        $path,
                        Json::encode($options),
                        true,
                    ),
                ),
                0,
                16,
            ),
        );
    }

    private static function getLastCommitDate(): ?string
    {
        static $cache = false;
        if ($cache === false) {
            $t = ArrayHelper::getValue(
                Yii::$app->params,
                'gitRevision.lastCommittedT',
            );
            $cache = is_int($t) ? gmdate('Ymd', $t) : null;
        }

        return $cache;
    }
}

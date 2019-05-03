<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Yii;
use app\models\Timezone;
use yii\helpers\StringHelper;

class UserTimeZone
{
    const COOKIE_KEY = 'timezone';

    public static function guess(bool $getDefault = true): ?Timezone
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $methods = [
                [static::class, 'guessByCookie'],
                [static::class, 'guessByAppLanguage'],
            ];

            foreach ($methods as $method) {
                if ($ret = call_user_func($method)) {
                    return $ret;
                }
            }

            if ($getDefault) {
                Yii::info("Returns default timezone, UTC", __METHOD__);
                return Timezone::findOne(['identifier' => 'Etc/UTC']);
            }

            return null;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function guessByCookie(): ?Timezone
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $cookie = Yii::$app->request->cookies->get(static::COOKIE_KEY);
            if (!$cookie) {
                return null;
            }

            $tz = Timezone::findOne(['identifier' => $cookie->value]);
            if ($tz) {
                Yii::info(
                    "Detected timezone by cookie, " . $tz->identifier,
                    __METHOD__
                );
            }

            return $tz;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function guessByAppLanguage(): ?Timezone
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);

            $map = [
                'en-GB' => 'Europe/London',
                'en*'   => 'America/Los_Angeles',
                'es-MX' => 'America/Mexico_City',
                'es*'   => 'Europe/Paris',
                'fr-CA' => 'America/New_York',
                'fr*'   => 'Europe/Paris',
                'it*'   => 'Europe/Paris',
                'ja*'   => 'Asia/Tokyo',
                'nl*'   => 'Europe/Paris',
                'ru*'   => 'Europe/Moscow',
            ];

            $wildcardOptions = [
                'caseSensitive' => false,
                'filePath' => false,
            ];

            $lang = Yii::$app->language;

            foreach ($map as $match => $ourTZ) {
                if (StringHelper::matchWildcard($match, $lang, $wildcardOptions)) {
                    $tz = Timezone::findOne(['identifier' => $ourTZ]);
                    if ($tz) {
                        Yii::info(
                            "Detected language by application language, " . $tz->identifier,
                            __METHOD__
                        );
                        return $tz;
                    }
                }
            }

            return null;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }
}

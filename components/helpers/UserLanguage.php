<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Yii;
use app\models\Language;
use yii\helpers\StringHelper;

class UserLanguage
{
    const PARAMETER_KEY = '_lang_';
    const COOKIE_KEY = 'language';

    public static function guess(bool $getDefault = true): ?Language
    {
        try {
            Yii::beginProfile('Detect language', __METHOD__);
            $methods = [
                [static::class, 'guessByParam'],
                [static::class, 'guessByCookie'],
                [static::class, 'guessByAcceptLanguage'],
            ];

            foreach ($methods as $method) {
                if ($ret = call_user_func($method)) {
                    return $ret;
                }
            }

            if ($getDefault) {
                Yii::info("Returns default language, en-US", __METHOD__);
                return Language::findOne(['lang' => 'en-US']);
            }

            return null;
        } finally {
            Yii::endProfile('Detect language', __METHOD__);
        }
    }

    public static function guessByParam(): ?Language
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $param = Yii::$app->request->get(static::PARAMETER_KEY);
            if (!is_scalar($param)) {
                return null;
            }

            $lang = Language::findOne(['lang' => (string)$param]);
            if ($lang) {
                Yii::info(
                    "Detected language by parameter, " . $lang->lang,
                    __METHOD__
                );
            }

            return $lang;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function guessByCookie(): ?Language
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $cookie = Yii::$app->request->cookies->get(static::COOKIE_KEY);
            if (!$cookie) {
                return null;
            }

            $lang = Language::findOne(['lang' => $cookie->value]);
            if ($lang) {
                Yii::info(
                    "Detected language by cookie, " . $lang->lang,
                    __METHOD__
                );
            }

            return $lang;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function guessByAcceptLanguage(): ?Language
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $request = Yii::$app->request;
            $acceptLangs = $request->acceptableLanguages;
            if (empty($acceptLangs) || static::isUABot((string)trim($request->userAgent))) {
                return null;
            }

            $map = [
                'en-au' => 'en-GB',
                'en-gb' => 'en-GB',
                'en*'   => 'en-US',
                'es-es' => 'es-ES',
                'es*'   => 'es-MX',
                'fr-ca' => 'fr-CA',
                'fr*'   => 'fr-FR',
                'it*'   => 'it-IT',
                'ja*'   => 'ja-JP',
                'nl*'   => 'nl-NL',
                'ru*'   => 'ru-RU',
            ];

            $wildcardOptions = [
                'caseSensitive' => false,
                'filePath' => false,
            ];
            foreach ($acceptLangs as $acceptLang) {
                foreach ($map as $match => $ourLang) {
                    if (StringHelper::matchWildcard($match, $acceptLang, $wildcardOptions)) {
                        $lang = Language::findOne(['lang' => $ourLang]);
                        if ($lang) {
                            Yii::info(
                                "Detected language by accept-language, " . $lang->lang,
                                __METHOD__
                            );
                            return $lang;
                        }
                    }
                }
            }

            return null;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    protected static function isUABot(string $ua): bool
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $crawlerDetect = new CrawlerDetect();
            if ($ua == '' || $crawlerDetect->isCrawler($ua)) {
                Yii::info("It looks this UA is a crawler: {$ua}", __METHOD__);
                return true;
            }

            return false;
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }
}

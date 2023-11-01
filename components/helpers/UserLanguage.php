<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Yii;
use app\models\AcceptLanguage;
use app\models\Language;

use function call_user_func;
use function is_scalar;
use function sprintf;
use function trim;

class UserLanguage
{
    public const PARAMETER_KEY = '_lang_';
    public const COOKIE_KEY = 'language';

    public static function guess(bool $getDefault = true): ?Language
    {
        try {
            Yii::beginProfile('Detect language', __METHOD__);
            $methods = [
                [self::class, 'guessByParam'],
                [self::class, 'guessByCookie'],
                [self::class, 'guessByAcceptLanguage'],
            ];

            foreach ($methods as $method) {
                if ($ret = call_user_func($method)) {
                    return $ret;
                }
            }

            if ($getDefault) {
                Yii::info('Returns default language, en-US', __METHOD__);
                return Language::find()
                    ->andWhere(['lang' => 'en-US'])
                    ->orderBy(null)
                    ->limit(1)
                    ->one();
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
            $param = Yii::$app->request->get(self::PARAMETER_KEY);
            if (!is_scalar($param)) {
                return null;
            }

            $lang = Language::find()
                ->andWhere(['lang' => (string)$param])
                ->orderBy(null)
                ->limit(1)
                ->one();
            if ($lang) {
                Yii::info(
                    'Detected language by parameter, ' . $lang->lang,
                    __METHOD__,
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
            $cookie = Yii::$app->request->cookies->get(self::COOKIE_KEY);
            if (!$cookie) {
                return null;
            }

            $lang = Language::find()
                ->andWhere(['lang' => $cookie->value])
                ->orderBy(null)
                ->limit(1)
                ->one();
            if ($lang) {
                Yii::info(
                    'Detected language by cookie, ' . $lang->lang,
                    __METHOD__,
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
            $userLangs = $request->acceptableLanguages;
            if (!$userLangs || self::isUABot((string)trim($request->userAgent))) {
                return null;
            }

            foreach ($userLangs as $userLang) {
                if ($model = AcceptLanguage::findMatched($userLang)) {
                    if ($lang = $model->language) {
                        Yii::info(
                            sprintf(
                                'Detected language %s by accept-language with rule #%d %s by %s',
                                $lang->lang,
                                $model->id,
                                $model->rule,
                                $userLang,
                            ),
                            __METHOD__,
                        );
                        return $lang;
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

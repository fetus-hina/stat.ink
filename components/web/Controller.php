<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use app\models\Language;
use app\models\Timezone;
use yii\base\Event;
use yii\base\View;
use yii\web\Controller as Base;
use yii\web\Cookie;

class Controller extends Base
{
    use HttpErrorTrait;

    public function init()
    {
        parent::init();
        $this->setLanguage();
        $this->setTimezone();
    }

    private function setLanguage(): void
    {
        $lang = (function (): ?Language {
            if ($val = $this->detectLanguageByParam()) {
                return $val;
            }

            if ($val = $this->detectLanguageByCookie()) {
                return $val;
            }

            if ($val = $this->detectLanguageByHeader()) {
                return $val;
            }

            return null;
        })();

        if ($lang) {
            Yii::$app->language = $lang->getLanguageId();
            Yii::$app->locale = $lang->lang;
        }
    }

    private function detectLanguageByParam(): ?Language
    {
        $param = Yii::$app->request->get('_lang_');
        if (!is_scalar($param)) {
            return null;
        }

        if (!$lang = Language::findOne(['lang' => (string)$param])) {
            return null;
        }

        if ($this->detectLanguageByCookie() === false) {
            Yii::$app->response->cookies->add(
                new Cookie([
                    'name' => 'language',
                    'value' => $lang->lang,
                    'expire' => time() + 86400 * 366,
                ])
            );
        }

        return $lang;
    }

    private function detectLanguageByCookie(): ?Language
    {
        $cookie = Yii::$app->request->cookies->get('language');
        if (!$cookie) {
            return null;
        }

        if (!$lang = Language::findOne(['lang' => $cookie->value])) {
            return null;
        }

        return $lang;
    }

    private function detectLanguageByHeader(): ?Language
    {
        $request = Yii::$app->request;
        $userAgent   = trim($request->userAgent);
        $acceptLangs = $request->acceptableLanguages;
        if (empty($acceptLangs) ||
                $userAgent == '' ||
                stripos($userAgent, 'bot') !== false ||
                stripos($userAgent, 'spider') !== false ||
                stripos($userAgent, 'google') !== false ||
                stripos($userAgent, 'http://') !== false ||
                stripos($userAgent, 'https://') !== false
        ) {
            return null;
        }

        //FIXME
        $firstLang = strtolower(array_shift($acceptLangs));
        $firstLangShort = substr($firstLang, 0, 2);
        switch ($firstLangShort) {
            case 'ja':
                return Language::findOne(['lang' => 'ja-JP']);

            case 'en':
                return Language::findOne([
                    'lang' => ($firstLang === 'en-gb' || $firstLang === 'en-au')
                        ? 'en-GB'
                        : 'en-US',
                ]);

            case 'es':
                return Language::findOne([
                    'lang' => $firstLang === 'es-es' ? 'es-ES' : 'es-MX',
                ]);

            case 'fr':
                return Language::findOne([
                    'lang' => $firstLang === 'fr-ca' ? 'fr-CA' : 'fr-FR',
                ]);

            case 'it':
                return Language::findOne(['lang' => 'it-IT']);

            case 'ru':
                return Language::findOne(['lang' => 'ru-RU']);

            case 'nl':
                return Language::findOne(['lang' => 'nl-NL']);

            default:
                return null;
        }
    }

    private function setTimezone()
    {
        $tz = (function () {
            $cookie = Yii::$app->request->cookies->get('timezone');
            if ($cookie) {
                $tz = Timezone::findOne(['identifier' => $cookie->value]);
                if ($tz) {
                    return $tz;
                }
            }
            switch (strtolower(Yii::$app->language)) {
                case 'en':
                case 'en-us':
                    return Timezone::findOne(['identifier' => 'America/Los_Angeles']);

                case 'en-gb':
                    return Timezone::findOne(['identifier' => 'Europe/London']);

                case 'es':
                case 'es-ES':
                case 'fr':
                case 'fr-FR':
                case 'it':
                case 'it-IT':
                case 'nl':
                case 'nl-NL':
                    return Timezone::findOne(['identifier' => 'Europe/Paris']);

                case 'es-MX':
                    return Timezone::findOne(['identifier' => 'America/Chicago']);

                case 'fr-CA':
                    return Timezone::findOne(['identifier' => 'America/Halifax']);

                case 'ru':
                case 'ru-RU':
                    return Timezone::findOne(['identifier' => 'Europe/Moscow']);

                default:
                    return Timezone::findOne(['identifier' => 'Asia/Tokyo']);
            }
        })();
        if ($tz) {
            Yii::$app->setTimeZone($tz->identifier);
            Yii::$app->setSplatoonRegion($tz->region_id);
        }
    }
}

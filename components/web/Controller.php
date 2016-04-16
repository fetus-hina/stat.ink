<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\base\Event;
use yii\base\View;
use yii\web\Controller as Base;
use yii\web\Cookie;
use app\models\Language;
use app\models\Timezone;

class Controller extends Base
{
    public function init()
    {
        parent::init();
        $this->setLanguage();
        $this->setTimezone();
    }

    private function setLanguage()
    {
        $lang = (function () {
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
            Yii::$app->language = $lang;
        }
    }

    private function detectLanguageByParam()
    {
        $param = Yii::$app->request->get('_lang_');
        if (!is_scalar($param)) {
            return false;
        }
        if (!$lang = Language::findOne(['lang' => (string)$param])) {
            return false;
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
        return $lang->lang;
    }

    private function detectLanguageByCookie()
    {
        $cookie = Yii::$app->request->cookies->get('language');
        if (!$cookie) {
            return false;
        }
        if (!$lang = Language::findOne(['lang' => $cookie->value])) {
            return false;
        }
        return $lang->lang;
    }

    private function detectLanguageByHeader()
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
            return false;
        }

        //FIXME
        $firstLang = strtolower(array_shift($acceptLangs));
        $firstLangShort = substr($firstLang, 0, 2);
        switch ($firstLangShort) {
            case 'ja':
                return 'ja-JP';

            case 'en':
                return ($firstLang === 'en-gb' || $firstLang === 'en-au')
                    ? 'en-GB'
                    : 'en-US';

            case 'es':
                return $firstLang === 'es-es' ? 'es-ES' : 'es-MX';

            default:
                return false;
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
                    return Timezone::findOne(['identifier' => 'Europe/Paris']);

                case 'es-MX':
                    return Timezone::findOne(['identifier' => 'America/Chicago']);

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

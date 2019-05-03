<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use app\components\helpers\UserLanguage;
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
        $lang = UserLanguage::guess();
        if ($lang) {
            Yii::$app->language = $lang->getLanguageId();
            Yii::$app->locale = $lang->lang;
            Yii::$app->response->cookies->add(new Cookie([
                'name' => UserLanguage::COOKIE_KEY,
                'value' => $lang->lang,
                'expire' => time() + 86400 * 366,
            ]));
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
            Yii::$app->formatter->timeZone = $tz->identifier;
            Yii::$app->setSplatoonRegion($tz->region_id);
        }
    }
}

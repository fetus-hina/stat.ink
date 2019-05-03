<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use app\components\helpers\UserLanguage;
use app\components\helpers\UserTimeZone;
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
        $tz = UserTimeZone::guess();
        if ($tz) {
            Yii::$app->setTimeZone($tz->identifier);
            Yii::$app->formatter->timeZone = $tz->identifier;
            Yii::$app->setSplatoonRegion($tz->region_id);
            Yii::$app->response->cookies->add(new Cookie([
                'name' => UserTimeZone::COOKIE_KEY,
                'value' => $tz->identifier,
                'expire' => time() + 86400 * 366,
            ]));
        }
    }
}

<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\web\Controller as Base;
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
        $cookie = Yii::$app->request->cookies->get('language');
        if ($cookie) {
            $lang = Language::findOne(['lang' => $cookie->value]);
            if ($lang) {
                Yii::$app->language = $lang->lang;
            }
        }
    }

    private function setTimezone()
    {
        $cookie = Yii::$app->request->cookies->get('timezone');
        if ($cookie) {
            $tz = Timezone::findOne(['identifier' => $cookie->value]);
            if ($tz) {
                Yii::$app->setTimeZone($tz->identifier);
            }
        }
    }
}

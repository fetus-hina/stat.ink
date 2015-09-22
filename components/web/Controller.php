<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\web;

use Yii;
use yii\web\Controller as Base;
use app\models\Timezone;

class Controller extends Base
{
    public function init()
    {
        parent::init();
        $this->setTimezone();
    }

    private function setTimezone()
    {
        $tzCookie = Yii::$app->request->cookies->get('timezone');
        if ($tzCookie) {
            $tz = Timezone::findOne(['zone' => $tzCookie->value]);
            if ($tz) {
                Yii::$app->setTimeZone($tz->zone);
            }
        }
    }
}

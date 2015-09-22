<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\timezone;

use DateTime;
use DateTimeZone;
use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Timezone;

class JsonAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $callback = $request->get('callback');
        if (!is_scalar($callback) || !preg_match('/^[A-Za-z0-9_.]+$/', $callback)) {
            $callback = null;
        }
        $now = new DateTime(
            sprintf('@%d', isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time())
        );
        $data = array_map(
            function (Timezone $tz) use ($now) {
                $tzInfo = new DateTimeZone($tz->zone);
                $offset = $tzInfo->getOffset($now);
                return [
                    'id' => $tz->zone,
                    'offset' => $now->setTimeZone($tzInfo)->format('P'),
                    'location' => $tzInfo->getLocation(),
                ];
            },
            Timezone::find()->all()
        );
        if ($callback !== null) {
            Yii::$app->getResponse()->format = 'jsonp';
            return [
                'data' => $data,
                'callback' => $callback,
            ];
        } else {
            Yii::$app->getResponse()->format = 'json';
            return $data;
        }
    }
}

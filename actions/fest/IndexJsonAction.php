<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\fest;

use DateTime;
use DateTimeZone;
use Yii;
use yii\web\ViewAction as BaseAction;
use app\components\Version;
use app\components\helpers\DateTimeFormatter;
use app\models\Fest;
use app\models\Timezone;

class IndexJsonAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $tz = $request->get('tz');
        $tz = new DateTimeZone(
            (!is_scalar($tz) || !Timezone::findOne(['zone' => $tz]))
                ? Yii::$app->timeZone
                : $tz
        );
        $callback = $request->get('callback');
        if (!is_scalar($callback) || !preg_match('/^[A-Za-z0-9_.]+$/', $callback)) {
            $callback = null;
        }

        $now = isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : microtime(true);
        $data = [ // {{{
            'now' => $now,
            'now_s' => DateTimeFormatter::unixTimeToString($now, $tz),
            'source' => [
                'name'      => Yii::$app->name,
                'url'       => Yii::$app->getUrlManager()->createAbsoluteUrl(['/fest/index']),
                'version'   => Version::getVersion(),
                'revision'  => [
                    Version::getRevision(),
                    Version::getShortRevision(),
                ],
            ],
            'fests' => array_map(
                function (Fest $fest) use ($tz) {
                    return $fest->toJsonArray($tz);
                },
                Fest::find()->orderBy('{{fest}}.[[id]] DESC')->all()
            ),
        ]; // }}}
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

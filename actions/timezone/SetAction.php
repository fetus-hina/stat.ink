<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/fest.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\timezone;

use Yii;
use yii\web\Cookie;
use yii\web\ViewAction as BaseAction;
use app\models\Timezone;

class SetAction extends BaseAction
{
    public function run()
    {
        $prev = date_default_timezone_get();
        $request = Yii::$app->getRequest();
        if ($request->isPost && $request->isAjax) {
            $reqZone = $request->post('zone');
            if (is_scalar($reqZone) && Timezone::findOne(['zone' => $reqZone])) {
                if (@date_default_timezone_set($reqZone)) {
                    Yii::$app->response->cookies->add(
                        new Cookie([
                            'name' => 'timezone',
                            'value' => $reqZone,
                            'expire' => time() + 86400 * 366,
                        ])
                    );
                }
            }
        }

        Yii::$app->getResponse()->format = 'json';
        return [
            'prev' => $prev,
            'now' => date_default_timezone_get(),
        ];
    }
}

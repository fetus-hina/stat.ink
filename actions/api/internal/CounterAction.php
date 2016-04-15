<?php
/**
 * @copyright Copyright (C) 2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Battle;
use app\models\User;

class CounterAction extends BaseAction
{
    public function run()
    {
        $response = Yii::$app->getResponse();
        $response->format = 'compact-json';

        return [
            'users' => User::getRoughCount(),
            'battles' => Battle::getRoughCount(),
        ];
    }
}

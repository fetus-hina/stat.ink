<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

declare(strict_types=1);

namespace app\actions\user;

use Yii;
use yii\web\ViewAction;
use yii\data\ActiveDataProvider;

class LoginHistoryAction extends ViewAction
{
    public function run()
    {
        $user = Yii::$app->getUser()->getIdentity();

        return $this->controller->render('login-history', [
            'dataProvider' => new ActiveDataProvider([
                'query' => $user->getLoginHistories()
                    ->with([
                        'method',
                        'userAgent',
                    ]),
                'pagination' => [
                    'pageSize' => 50,
                ],
                'sort' => false,
            ]),
        ]);
    }
}

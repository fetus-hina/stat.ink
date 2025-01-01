<?php

/**
 * @copyright Copyright (C) 2018-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show\v2;

use Yii;
use app\models\Rule2;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class UserStatGachiAction extends BaseAction
{
    private $user;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $this->user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        if (!$rule = Rule2::findOne(['key' => $request->get('rule')])) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        return $this->controller->render('user-stat-gachi', [
            'user' => $this->user,
            'rule' => $rule,
        ]);
    }
}

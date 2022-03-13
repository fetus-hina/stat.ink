<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show;

use Yii;
use app\components\helpers\T;
use app\models\User;
use yii\base\Action;
use yii\web\NotFoundHttpException;

final class UserStatWeaponAction extends Action
{
    public function run(): string
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        return T::webController($this->controller)
            ->redirect(
                ['show/user-stat-by-weapon', 'screen_name' => $user->screen_name],
                301
            );
    }
}

<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\User;

class UserStatWeaponAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $list = array_map(
            function ($model) {
                return (object)[
                    'key' => $model->weapon->key,
                    'name' => Yii::t('app-weapon', $model->weapon->name),
                    'count' => $model->count,
                ];
            },
            $user->userWeapons
        );

        usort($list, function ($a, $b) {
            if ($a->count !== $b->count) {
                return $b->count - $a->count;
            }
            return strcasecmp($a->name, $b->name);
        });

        return $this->controller->render('user-stat-weapon.tpl', [
            'user' => $user,
            'list' => $list,
        ]);
    }
}

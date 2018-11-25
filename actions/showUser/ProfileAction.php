<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\showUser;

use Yii;
use app\models\User;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class ProfileAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        if (!$user = User::findOne(['screen_name' => $request->get('screen_name')])) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $battles1 = $user->getBattles()
            ->with([
                'lobby',
                'rule',
                'rule.mode',
                'map',
                'weapon',
                'weapon.subweapon',
                'weapon.special',
                'rank',
                'rankAfter',
            ])
            ->orderBy(['battle.id' => SORT_DESC])
            ->limit(5)
            ->all();

        $battles2 = $user->getBattle2s()
            ->with([
                'lobby',
                'rule',
                'mode',
                'map',
                'weapon',
                'weapon.subweapon',
                'weapon.special',
                'rank',
                'rankAfter',
            ])
            ->orderBy(['battle2.id' => SORT_DESC])
            ->limit(5)
            ->all();

        list($activityFrom, $activityTo) = $user->getActivityDisplayRange();
        $activity1 = $user->getActivitiesForSplatoon1();
        $activity2 = $user->getActivitiesForSplatoon2();

        $permLink = Url::to(['show-user/profile', 'screen_name' => $user->screen_name], true);

        return $this->controller->render('profile', [
            'user' => $user,
            'battles1' => $battles1,
            'battles2' => $battles2,
            'activity1' => $activity1,
            'activity2' => $activity2,
            'activityFrom' => $activityFrom,
            'activityTo' => $activityTo,
            'permLink'  => $permLink,
        ]);
    }
}

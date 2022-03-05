<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\showUser;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\User;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use const SORT_DESC;

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

        $permLink = Url::to(['show-user/profile', 'screen_name' => $user->screen_name], true);
        [$activityFrom, $activityTo] = BattleHelper::getActivityDisplayRange();

        return $this->controller->render('profile', [
            'user' => $user,
            'battles1' => $battles1,
            'battles2' => $battles2,
            'activityFrom' => $activityFrom,
            'activityTo' => $activityTo,
            'permLink'  => $permLink,
        ]);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\showUser;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Battle;
use app\models\User;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

final class ProfileAction extends Action
{
    public function run(): string
    {
        $request = Yii::$app->request;
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $battles1 = Battle::find()
            ->andWhere(['user_id' => $user->id])
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

        $battles2 = Battle2::find()
            ->andWhere(['user_id' => $user->id])
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

        $battles3 = Battle3::find()
            ->andWhere([
                'user_id' => $user->id,
                'is_deleted' => false,
            ])
            ->with([
                'lobby',
                'map',
                'rankAfter',
                'rankBefore',
                'result',
                'rule',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->orderBy(['id' => SORT_DESC])
            ->limit(5)
            ->all();

        $permLink = Url::to(['show-user/profile', 'screen_name' => $user->screen_name], true);
        [$activityFrom, $activityTo] = BattleHelper::getActivityDisplayRange();

        return $this->controller->render('profile', [
            'activityFrom' => $activityFrom,
            'activityTo' => $activityTo,
            'battles1' => $battles1,
            'battles2' => $battles2,
            'battles3' => $battles3,
            'permLink'  => $permLink,
            'user' => $user,
        ]);
    }
}

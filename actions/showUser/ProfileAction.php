<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\showUser;

use Yii;
use app\components\helpers\Battle as BattleHelper;
use app\models\Battle;
use app\models\Battle2;
use app\models\User;
use yii\base\Action;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

use const SORT_DESC;

final class ProfileAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        if (!$user = User::findOne(['screen_name' => $request->get('screen_name')])) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $battles1 = Battle::find()
            ->andWhere(['user_id' => $user->id])
            ->with([
                'lobby',
                'map',
                'rank',
                'rankAfter',
                'rule',
                'rule.mode',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
            ])
            ->orderBy(['battle.id' => SORT_DESC])
            ->limit(5)
            ->all();

        $battles2 = Battle2::find()
            ->andWhere(['user_id' => $user->id])
            ->with([
                'lobby',
                'map',
                'mode',
                'rank',
                'rankAfter',
                'rule',
                'weapon',
                'weapon.special',
                'weapon.subweapon',
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

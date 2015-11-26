<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\BattleFilterForm;
use app\models\Battle;
use app\models\User;

class UserAction extends BaseAction
{
    use FilterFormTrait;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $battle = Battle::find()
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
            ]);

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        if ($filter->validate()) {
            $battle->filter($filter);
        }
        $summary = $battle->summary;

        $permLink = Url::to(
            array_merge(
                ['show/user', 'screen_name' => $user->screen_name],
                $filter->hasErrors() ? [] : $filter->toPermLink()
            ),
            true
        );

        $isPjax = $request->isPjax;
        return $this->controller->render('user.tpl', array_merge(
            [
                'user'      => $user,
                'battleDataProvider' => new ActiveDataProvider([
                    'query' => $battle,
                    'pagination' => ['pageSize' => 100 ]
                ]),
                'summary'   => $summary,
                'filter'    => $filter,
                'permLink'  => $permLink
            ],
            $this->makeFilterFormData($user)
        ));
    }
}

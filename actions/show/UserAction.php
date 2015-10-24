<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\BattleFilterForm;
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

        $battle = $user->getBattles()
            ->with(['rule', 'map', 'weapon', 'weapon.subweapon', 'weapon.special']);

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        if ($filter->validate()) {
            $battle->filter($filter);
        }
        $summary = $battle->summary;

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
            ],
            $this->makeFilterFormData($user)
        ));
    }
}

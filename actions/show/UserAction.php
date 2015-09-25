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
use app\models\BattleFilterForm;
use app\models\GameMode;
use app\models\Map;
use app\models\Rule;
use app\models\User;
use app\models\Weapon;
use app\models\WeaponType;

class UserAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException('指定されたユーザが見つかりません');
        }

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        return $this->controller->render('user.tpl', [
            'user' => $user,
            'filter' => $filter,
            'rules' => $this->makeRulesList(),
            'maps' => $this->makeMapsList(),
            'weapons' => $this->makeWeaponsList(),
        ]);
    }

    private function makeRulesList()
    {
        $ret = ['' => '全てのルール'];
        $gameModes = GameMode::find()->orderBy('[[id]] ASC')->all();
        foreach ($gameModes as $gameMode) {
            $rules = Rule::find()
                ->andWhere(['mode_id' => $gameMode->id])
                ->orderBy('[[id]] ASC')
                ->all();
            $mode = [];
            if (count($rules) > 1) {
                $mode['@' . $gameMode->key] = '全ての' . $gameMode->name;
            }
            foreach ($rules as $rule) {
                $mode[$rule->key] = $rule->name;
            }
            $ret[$gameMode->name] = $mode;
        }
        return $ret;
    }

    private function makeMapsList()
    {
        $ret = ['' => '全てのマップ'];
        $maps = Map::find()->orderBy('[[name]] ASC')->all();
        usort($maps, function ($a, $b) {
            return strnatcasecmp($a->name, $b->name);
        });
        foreach ($maps as $map) {
            $ret[$map->key] = $map->name;
        }
        return $ret;
    }

    private function makeWeaponsList()
    {
        $ret = ['' => '全てのブキ'];
        $types = WeaponType::find()->orderBy('[[id]] ASC')->all();
        foreach ($types as $type) {
            $weapons = Weapon::find()->andWhere(['type_id' => $type->id])->all();
            usort($weapons, function ($a, $b) {
                return strnatcasecmp($a->name, $b->name);
            });

            $tmp = [];
            if (count($weapons) > 1) {
                $tmp['@' . $type->key] = '全ての' . $type->name;
            }
            foreach ($weapons as $weapon) {
                $tmp[$weapon->key] = $weapon->name;
            }
            $ret[$type->name] = $tmp;
        }
        return $ret;
    }
}

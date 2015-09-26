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
        $ret = [
            '' => Yii::t('app-rule', 'Any Rule'),
        ];
        $gameModes = GameMode::find()->orderBy('[[id]] ASC')->all();
        foreach ($gameModes as $gameMode) {
            $gameModeText = Yii::t('app-rule', $gameMode->name); // "ナワバリバトル"
            $rules = Rule::find()
                ->andWhere(['mode_id' => $gameMode->id])
                ->orderBy('[[id]] ASC')
                ->all();
            $mode = [];
            if (count($rules) > 1) {
                $mode['@' . $gameMode->key] = Yii::t('app-rule', 'All of {0}', $gameModeText);
            }
            foreach ($rules as $rule) {
                $mode[$rule->key] = Yii::t('app-rule', $rule->name);
            }
            $ret[$gameModeText] = $mode;
        }
        return $ret;
    }

    private function makeMapsList()
    {
        $ret = [];
        foreach (Map::find()->all() as $map) {
            $ret[$map->key] = Yii::t('app-map', $map->name);
        }
        asort($ret);
        return array_merge(
            ['' => Yii::t('app-map', 'Any Map')],
            $ret
        );
    }

    private function makeWeaponsList()
    {
        $ret = [];
        $types = WeaponType::find()->orderBy('[[id]] ASC')->all();
        foreach ($types as $type) {
            $typeName = Yii::t('app-weapon', $type->name);

            $tmp = [];
            $weapons = Weapon::find()->andWhere(['type_id' => $type->id])->all();
            foreach ($weapons as $weapon) {
                $tmp[$weapon->key] = Yii::t('app-weapon', $weapon->name);
            }
            asort($tmp);
            if (count($tmp) > 1) {
                $ret[$typeName] = array_merge(
                    ['@' . $type->key => Yii::t('app-weapon', 'All of {0}', $typeName)],
                    $tmp
                );
            } else {
                $ret[$typeName] = $tmp;
            }
        }
        return array_merge(
            [ '' => Yii::t('app-weapon', 'Any Weapon') ],
            $ret
        );
    }
}

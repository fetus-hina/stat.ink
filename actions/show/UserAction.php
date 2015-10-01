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
use app\models\GameMode;
use app\models\Map;
use app\models\Rule;
use app\models\Special;
use app\models\Subweapon;
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

        $isPjax = $request->isPjax;
        return $this->controller->render('user.tpl', [
            'user'      => $user,
            'battleDataProvider' => new ActiveDataProvider([
                'query' => $battle,
                'pagination' => ['pageSize' => 100 ]
            ]),
            'filter'    => $filter,
            'rules'     => $isPjax ? [] : $this->makeRulesList(),
            'maps'      => $isPjax ? [] : $this->makeMapsList(),
            'weapons'   => $isPjax ? [] : $this->makeWeaponsList(),
            'results'   => [
                ''      => Yii::t('app', 'Won / Lost'),
                'win'   => Yii::t('app', 'Won'),
                'lose'  => Yii::t('app', 'Lost'),
            ],
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
        return array_merge(
            $this->makeWeaponsListWeaponsAndTypes(),
            $this->makeWeaponsListSubweapons(),
            $this->makeWeaponsListSpecials()
        );
    }

    private function makeWeaponsListWeaponsAndTypes()
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

    private function makeWeaponsListSubweapons()
    {
        $ret = [];
        foreach (Subweapon::find()->all() as $item) {
            $ret['+' . $item->key] = Yii::t('app-subweapon', $item->name);
        }
        asort($ret);
        return [
            Yii::t('app', 'Sub Weapon') => $ret
        ];
    }

    private function makeWeaponsListSpecials()
    {
        $ret = [];
        foreach (Special::find()->all() as $item) {
            $ret['*' . $item->key] = Yii::t('app-special', $item->name);
        }
        asort($ret);
        return [
            Yii::t('app', 'Special') => $ret
        ];
    }
}

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
use app\models\Lobby;
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
            'lobbies'   => $isPjax ? [] : $this->makeLobbiesList(),
            'rules'     => $isPjax ? [] : $this->makeRulesList(),
            'maps'      => $isPjax ? [] : $this->makeMapsList(),
            'weapons'   => $isPjax ? [] : $this->makeWeaponsList($user),
            'results'   => [
                ''      => Yii::t('app', 'Won / Lost'),
                'win'   => Yii::t('app', 'Won'),
                'lose'  => Yii::t('app', 'Lost'),
            ],
        ]);
    }

    private function makeLobbiesList()
    {
        $ret = [
            '' => Yii::t('app-rule', 'Any Game Mode'),
        ];
        $tmpList = Lobby::find()->orderBy('[[id]] ASC')->all();
        foreach ($tmpList as $lobby) {
            $ret[$lobby->key] = Yii::t('app-rule', $lobby->name);
        }
        return $ret;
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

    private function makeWeaponsList(User $user)
    {
        return array_merge(
            $this->makeWeaponsListWeaponsAndTypes($user),
            $this->makeWeaponsListSubweapons($user),
            $this->makeWeaponsListSpecials($user)
        );
    }

    private function makeWeaponsListWeaponsAndTypes(User $user)
    {
        $userUsedWeapons = $this->getUsedWeaponIdList($user);

        $ret = [];
        $types = WeaponType::find()->orderBy('[[id]] ASC')->all();
        foreach ($types as $type) {
            $typeName = Yii::t('app-weapon', $type->name);

            $tmp = [];
            $weapons = Weapon::find()
                ->andWhere(['{{weapon}}.[[type_id]]' => $type->id])
                ->andWhere(['in', '{{weapon}}.[[id]]', $userUsedWeapons])
                ->all();
            foreach ($weapons as $weapon) {
                $tmp[$weapon->key] = Yii::t('app-weapon', $weapon->name);
            }
            asort($tmp);
            if (count($tmp) > 1) {
                $ret[$typeName] = array_merge(
                    ['@' . $type->key => Yii::t('app-weapon', 'All of {0}', $typeName)],
                    $tmp
                );
            } elseif (count($tmp) === 1) {
                $ret[$typeName] = $tmp;
            }
        }
        return array_merge(
            [ '' => Yii::t('app-weapon', 'Any Weapon') ],
            $ret
        );
    }

    private function makeWeaponsListSubweapons(User $user)
    {
        $query = Subweapon::find()
            ->andWhere(['in', 'id',
                array_map(
                    function ($model) {
                        return $model->subweapon_id;
                    },
                    Weapon::findAll($this->getUsedWeaponIdList($user))
                )
            ]);

        $ret = [];
        foreach ($query->all() as $item) {
            $ret['+' . $item->key] = Yii::t('app-subweapon', $item->name);
        }
        if (count($ret) < 2) {
            return [];
        }
        asort($ret);
        return [
            Yii::t('app', 'Sub Weapon') => $ret
        ];
    }

    private function makeWeaponsListSpecials(User $user)
    {
        $query = Special::find()
            ->andWhere(['in', 'id',
                array_map(
                    function ($model) {
                        return $model->special_id;
                    },
                    Weapon::findAll($this->getUsedWeaponIdList($user))
                )
            ]);

        $ret = [];
        foreach ($query->all() as $item) {
            $ret['*' . $item->key] = Yii::t('app-special', $item->name);
        }
        if (count($ret) < 2) {
            return [];
        }
        asort($ret);
        return [
            Yii::t('app', 'Special') => $ret
        ];
    }

    private function getUsedWeaponIdList(User $user)
    {
        return array_map(
            function ($model) {
                return (int)$model->weapon_id;
            },
            $user->userWeapons
        );
    }
}

<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show\v2;

use Yii;
use app\models\Battle2FilterForm;
use app\models\Map2;
use app\models\Rule2;
use app\models\User;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class UserStatByMapRuleAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $filter = new Battle2FilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        return $this->controller->render('user-stat-by-map-rule', array_merge(
            [
                'user' => $user,
                'filter' => $filter,
            ],
            $this->getData($user, $filter)
        ));
    }

    private function getData(User $user, Battle2FilterForm $filter)
    {
        $query = (new \yii\db\Query())
            ->select([
                'map_key'   => 'MAX({{map2}}.[[key]])',
                'rule_key'  => 'MAX({{rule2}}.[[key]])',
                'result'    => '(CASE WHEN {{battle2}}.[[is_win]] = TRUE THEN \'win\' ELSE \'lose\' END)',
                'count'     => 'COUNT(*)',
            ])
            ->from('battle2')
            ->innerJoin('map2', '{{battle2}}.[[map_id]] = {{map2}}.[[id]]')
            ->innerJoin('rule2', '{{battle2}}.[[rule_id]] = {{rule2}}.[[id]]')
            ->leftJoin('lobby2', '{{battle2}}.[[lobby_id]] = {{lobby2}}.[[id]]')
            // ->leftJoin('game_mode', '{{rule2}}.[[mode_id]] = {{mode2}}.[[id]]')
            ->leftJoin('weapon2', '{{battle2}}.[[weapon_id]] = {{weapon2}}.[[id]]')
            ->leftJoin('weapon_type2', '{{weapon2}}.[[type_id]] = {{weapon_type2}}.[[id]]')
            ->leftJoin('subweapon2', '{{weapon2}}.[[subweapon_id]] = {{subweapon2}}.[[id]]')
            ->leftJoin('special2', '{{weapon2}}.[[special_id]] = {{special2}}.[[id]]')
            ->leftJoin('rank2', '{{battle2}}.[[rank_id]] = {{rank2}}.[[id]]')
            ->leftJoin('rank_group2', '{{rank2}}.[[group_id]] = {{rank_group2}}.[[id]]')
            ->andWhere(['{{battle2}}.[[user_id]]' => $user->id])
            ->andWhere(['in', '{{battle2}}.[[is_win]]', [ true, false ]])
            ->groupBy([
                '{{battle2}}.[[map_id]]',
                '{{battle2}}.[[rule_id]]',
                '{{battle2}}.[[is_win]]',
            ]);
        
        $maps = Map2::find()->all();
        $rules = Rule2::find()->all();

        $ret = ['total' => []];
        foreach ($rules as $rule) {
            $ret['total'][$rule->key] = [
                'win' => 0,
                'lose' => 0,
            ];
        }
        foreach ($maps as $map) {
            $tmp = [];
            foreach ($rules as $rule) {
                $tmp[$rule->key] = [
                    'win' => 0,
                    'lose' => 0,
                ];
            }
            $ret[$map->key] = $tmp;
        }

        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            $ret[$row->map_key][$row->rule_key][$row->result] = (int)$row->count;
            $ret['total'][$row->rule_key][$row->result] += (int)$row->count;
        }

        $maps2 = [];
        foreach ($maps as $map) {
            $maps2[$map->key] = Yii::t('app-map2', $map->name);
        }
        asort($maps2);

        $rules2 = [];
        foreach ($rules as $rule) {
            $rules2[$rule->key] = Yii::t('app-rule2', $rule->name);
        }
        asort($rules2);

        return [
            'data' => $ret,
            'mapNames' => $maps2,
            'ruleNames' => $rules2,
        ];
    }
}

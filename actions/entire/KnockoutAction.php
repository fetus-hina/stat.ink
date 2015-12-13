<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Rule;
use app\models\Map;

class KnockoutAction extends BaseAction
{
    public function run()
    {
        $rules = [];
        $query = Rule::find()
            ->innerJoinWith('mode')
            ->andWhere(['{{game_mode}}.[[key]]' => 'gachi']);
        foreach ($query->all() as $rule) {
            $rules[$rule->id] = Yii::t('app-rule', $rule->name);
        }
        asort($rules);

        $maps = [];
        foreach (Map::find()->all() as $map) {
            $maps[$map->id] = [
                $map->key,
                Yii::t('app-map', $map->name)
            ];
        }
        uasort($maps, function ($a, $b) {
            return strnatcasecmp($a[1], $b[1]);
        });

        // init data
        $data = [];
        foreach (array_keys($maps) as $mapId) {
            $data[$mapId] = [];
            foreach (array_keys($rules) as $ruleId) {
                $data[$mapId][$ruleId] = (object)[
                    'battle_count' => 0,
                    'ko_count' => 0,
                ];
            }
        }

        // set data
        foreach ($this->query() as $row) {
            $ruleId = $row['rule_id'];
            $mapId = $row['map_id'];
            $data[$mapId][$ruleId]->battle_count = (int)$row['battle_count'];
            $data[$mapId][$ruleId]->ko_count = (int)$row['ko_count'];
        }

        return $this->controller->render('knockout.tpl', [
            'rules' => $rules,
            'maps' => $maps,
            'data' => $data,
        ]);
    }

    private function query()
    {
        $query = (new \yii\db\Query())
            ->select([
                'rule_id' => '{{battle}}.[[rule_id]]',
                'map_id' => '{{battle}}.[[map_id]]',
                'battle_count' => 'COUNT({{battle}}.*)',
                'ko_count' => sprintf(
                    'SUM(%s)',
                    'CASE WHEN {{battle}}.[[is_knock_out]] THEN 1 ELSE 0 END'
                ),
            ])
            ->from('battle')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->andWhere('{{battle}}.[[map_id]] IS NOT NULL')
            ->andWhere('{{battle}}.[[is_win]] IS NOT NULL')
            ->andWhere('{{battle}}.[[is_knock_out]] IS NOT NULL')
            ->andWhere([
                '{{game_mode}}.[[key]]' => 'gachi',
                '{{battle}}.[[is_automated]]' => true,
            ])
            ->groupBy(['{{battle}}.[[rule_id]]', '{{battle}}.[[map_id]]']);
        return $query->createCommand()->queryAll();
    }
}

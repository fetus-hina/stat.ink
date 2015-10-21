<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\api\internal;

use Yii;
use yii\db\Query;

class StatByRuleAction extends BaseStatAction
{
    protected function makeData()
    {
        $query = (new Query())
            ->select([
                'rule_key'  => 'MAX({{rule}}.[[key]])',
                'rule_name' => 'MAX({{rule}}.[[name]])',
                'mode_key'  => 'MAX({{game_mode}}.[[key]])',
                'mode_name' => 'MAX({{game_mode}}.[[name]])',
                'result'    => '(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN \'win\' ELSE \'lose\' END)',
                'count'     => 'COUNT(*)',
            ])
            ->from('battle')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $this->user->id])
            ->andWhere(['in', '{{battle}}.[[is_win]]', [ true, false ]])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->groupBy(['{{battle}}.[[rule_id]]', '{{battle}}.[[is_win]]']);
        $modes = [];
        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            if (!isset($modes[$row->mode_key])) {
                $modes[$row->mode_key] = [
                    'name' => $row->mode_name,
                    'rules' => [],
                ];
            }
            if (!isset($modes[$row->mode_key]['rules'][$row->rule_key])) {
                $modes[$row->mode_key]['rules'][$row->rule_key] = [
                    'name' => $row->rule_name,
                    'win' => 0,
                    'lose' => 0,
                ];
            }
            $modes[$row->mode_key]['rules'][$row->rule_key][$row->result] = (int)$row->count;
        }
        return $modes;
    }

    protected function decorate($modes)
    {
        foreach ($modes as &$mode) {
            $mode['name'] = Yii::t('app-rule', $mode['name']);
            foreach ($mode['rules'] as &$rule) {
                $rule['name'] = Yii::t('app-rule', $rule['name']);
            }
        }
        return $modes;
    }
}

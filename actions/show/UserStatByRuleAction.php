<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\show;

use Yii;
use app\models\BattleFilterForm;
use app\models\User;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class UserStatByRuleAction extends BaseAction
{
    use UserStatFilterTrait;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        $filter = new BattleFilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        return $this->controller->render('user-stat-by-rule', [
            'user' => $user,
            'filter' => $filter,
            'data' => $this->getData($user, $filter),
        ]);
    }

    private function getData(User $user, BattleFilterForm $filter)
    {
        $query = (new Query())
            ->select([
                'rule_key' => 'MAX({{rule}}.[[key]])',
                'rule_name' => 'MAX({{rule}}.[[name]])',
                'mode_key' => 'MAX({{game_mode}}.[[key]])',
                'mode_name' => 'MAX({{game_mode}}.[[name]])',
                'result' => '(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN \'win\' ELSE \'lose\' END)',
                'count' => 'COUNT(*)',
            ])
            ->from('battle')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
            ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $user->id])
            ->andWhere(['in', '{{battle}}.[[is_win]]', [true, false]])
            ->groupBy(['{{battle}}.[[rule_id]]', '{{battle}}.[[is_win]]']);

        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }

        $modes = [];
        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            if (!isset($modes[$row->mode_key])) {
                $modes[$row->mode_key] = [
                    'name' => Yii::t('app-rule', $row->mode_name),
                    'rules' => [],
                ];
            }
            if (!isset($modes[$row->mode_key]['rules'][$row->rule_key])) {
                $modes[$row->mode_key]['rules'][$row->rule_key] = [
                    'name' => Yii::t('app-rule', $row->rule_name),
                    'win' => 0,
                    'lose' => 0,
                ];
            }
            $modes[$row->mode_key]['rules'][$row->rule_key][$row->result] = (int)$row->count;
        }
        return $modes;
    }
}

<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
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

class UserStatByMapAction extends BaseAction
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
        // if ($filter->validate()) {
        //     $battle->filter($filter);
        // }

        return $this->controller->render('user-stat-by-map', [
            'user' => $user,
            'filter' => $filter,
            'data' => $this->getData($user, $filter),
        ]);
    }

    private function getData(User $user, BattleFilterForm $filter)
    {
        $query = (new Query())
            ->select([
                'map_key' => 'MAX({{map}}.[[key]])',
                'map_name' => 'MAX({{map}}.[[name]])',
                'result' => '(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN \'win\' ELSE \'lose\' END)',
                'count' => 'COUNT(*)',
            ])
            ->from('battle')
            ->innerJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
            ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]')
            ->andWhere(['{{battle}}.[[user_id]]' => $user->id])
            ->andWhere(['in', '{{battle}}.[[is_win]]', [true, false]])
            ->groupBy(['{{battle}}.[[map_id]]', '{{battle}}.[[is_win]]']);

        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }

        $maps = [];
        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            if (!isset($maps[$row->map_key])) {
                $maps[$row->map_key] = [
                    'name' => Yii::t('app-map', $row->map_name),
                    'win' => 0,
                    'lose' => 0,
                ];
            }
            $maps[$row->map_key][$row->result] = (int)$row->count;
        }
        return $maps;
    }
}

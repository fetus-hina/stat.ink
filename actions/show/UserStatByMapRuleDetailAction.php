<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show;

use Yii;
use app\models\BattleFilterForm;
use app\models\Map;
use app\models\Rule;
use app\models\User;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_merge;
use function asort;
use function implode;
use function sprintf;

class UserStatByMapRuleDetailAction extends BaseAction
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

        return $this->controller->render('user-stat-by-map-rule-detail', array_merge(
            [
                'user' => $user,
                'filter' => $filter,
            ],
            $this->getData($user, $filter),
        ));
    }

    private function getData(User $user, BattleFilterForm $filter)
    {
        $nawabari = Rule::findOne(['key' => 'nawabari'])->id;
        $query = (new Query())
            ->select([
                'map_key' => 'MAX({{map}}.[[key]])',
                'rule_key' => 'MAX({{rule}}.[[key]])',

                // 勝敗データ
                'win' => 'SUM(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)',
                'lose' => 'SUM(CASE WHEN {{battle}}.[[is_win]] = FALSE THEN 1 ELSE 0 END)',

                // KO/時間切れ勝敗データ
                'win_ko' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{battle}}.[[rule_id]] = {$nawabari} THEN 0",
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] = FALSE THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] = FALSE THEN 0',
                    'ELSE 1',
                ])),
                'lose_ko' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{battle}}.[[rule_id]] = {$nawabari} THEN 0",
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] = TRUE THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] = FALSE THEN 0',
                    'ELSE 1',
                ])),
                'win_to' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{battle}}.[[rule_id]] = {$nawabari} THEN 0",
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] = FALSE THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] = TRUE THEN 0',
                    'ELSE 1',
                ])),
                'lose_to' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {{battle}}.[[rule_id]] = {$nawabari} THEN 0",
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] = TRUE THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_knock_out]] = TRUE THEN 0',
                    'ELSE 1',
                ])),

                // キルデス
                'battles_kd' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[death]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
                'kill_sum' => 'SUM(COALESCE({{battle}}.[[kill]], 0))',
                'death_sum' => 'SUM(COALESCE({{battle}}.[[death]], 0))',

                // ポイント
                'battles_pt' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[rule_id]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[bonus_id]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[my_point]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    "WHEN {{battle}}.[[rule_id]] <> '{$nawabari}' THEN 0",
                    'ELSE 1',
                ])),
                'point_sum' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[rule_id]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[bonus_id]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[my_point]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] = TRUE THEN {{battle}}.[[my_point]] - {{turfwar_win_bonus}}.[[bonus]]',
                    'ELSE {{battle}}.[[my_point]]',
                ])),
                'point_max' => sprintf('MAX(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[rule_id]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[bonus_id]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[my_point]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[is_win]] = TRUE THEN {{battle}}.[[my_point]] - {{turfwar_win_bonus}}.[[bonus]]',
                    'ELSE {{battle}}.[[my_point]]',
                ])),

                // 試合時間
                'battles_time' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[start_at]] >= {{battle}}.[[end_at]] THEN 0',
                    "WHEN ({{battle}}.[[end_at]] - {{battle}}.[[start_at]]) >= '10 minutes'::interval THEN 0",
                    'ELSE 1',
                ])),
                'time_sum' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle}}.[[start_at]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[end_at]] IS NULL THEN 0',
                    'WHEN {{battle}}.[[start_at]] >= {{battle}}.[[end_at]] THEN 0',
                    "WHEN ({{battle}}.[[end_at]] - {{battle}}.[[start_at]]) >= '10 minutes'::interval THEN 0",
                    'ELSE EXTRACT(EPOCH FROM ({{battle}}.[[end_at]] - {{battle}}.[[start_at]]))',
                ])),
            ])
            ->from('battle')
            ->innerJoin('map', '{{battle}}.[[map_id]] = {{map}}.[[id]]')
            ->innerJoin('rule', '{{battle}}.[[rule_id]] = {{rule}}.[[id]]')
            ->innerJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->leftJoin('game_mode', '{{rule}}.[[mode_id]] = {{game_mode}}.[[id]]')
            ->leftJoin('weapon', '{{battle}}.[[weapon_id]] = {{weapon}}.[[id]]')
            ->leftJoin('weapon_type', '{{weapon}}.[[type_id]] = {{weapon_type}}.[[id]]')
            ->leftJoin('subweapon', '{{weapon}}.[[subweapon_id]] = {{subweapon}}.[[id]]')
            ->leftJoin('special', '{{weapon}}.[[special_id]] = {{special}}.[[id]]')
            ->leftJoin('rank', '{{battle}}.[[rank_id]] = {{rank}}.[[id]]')
            ->leftJoin('rank_group', '{{rank}}.[[group_id]] = {{rank_group}}.[[id]]')
            ->leftJoin('turfwar_win_bonus', '{{battle}}.[[bonus_id]] = {{turfwar_win_bonus}}.[[id]]')
            ->andWhere([
                '{{battle}}.[[user_id]]' => $user->id,
                '{{battle}}.[[is_win]]' => [true, false],
                '{{lobby}}.[[key]]' => ['standard', 'fest'],
            ])
            ->groupBy(['{{battle}}.[[map_id]]', '{{battle}}.[[rule_id]]']);

        if ($filter && !$filter->hasErrors()) {
            $this->filter($query, $filter);
        }

        $maps = Map::find()->all();
        $rules = Rule::find()->all();

        $ret = [];
        foreach ($maps as $map) {
            $tmp = [];
            foreach ($rules as $rule) {
                $tmp[$rule->key] = (object)[
                    'win' => 0,
                    'lose' => 0,
                    'win_ko' => 0,
                    'lose_ko' => 0,
                    'win_to' => 0,
                    'lose_to' => 0,
                    'battles_kd' => 0,
                    'kill_sum' => 0,
                    'death_sum' => 0,
                    'battles_pt' => 0,
                    'point_sum' => 0,
                    'point_max' => 0,
                    'battles_time' => 0,
                    'time_sum' => 0,
                ];
            }
            $ret[$map->key] = $tmp;
        }

        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            $ret[$row->map_key][$row->rule_key] = $row;
        }

        $maps2 = [];
        foreach ($maps as $map) {
            $maps2[$map->key] = (object)[
                'name' => Yii::t('app-map', $map->name),
                'short' => Yii::t('app-map', $map->short_name),
            ];
        }
        asort($maps2);

        $rules2 = [];
        foreach ($rules as $rule) {
            $rules2[$rule->key] = Yii::t('app-rule', $rule->name);
        }
        asort($rules2);

        return [
            'data' => $ret,
            'mapNames' => $maps2,
            'ruleNames' => $rules2,
        ];
    }
}

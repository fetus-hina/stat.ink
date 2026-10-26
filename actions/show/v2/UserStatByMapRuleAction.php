<?php

/**
 * @copyright Copyright (C) 2017-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\actions\show\v2;

use Override;
use Yii;
use app\models\Battle2FilterForm;
use app\models\Map2;
use app\models\Rule2;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_merge;
use function implode;
use function sprintf;
use function substr;

use const SORT_ASC;

class UserStatByMapRuleAction extends BaseAction
{
    #[Override]
    public function run(): string
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne([
            'screen_name' => $request->get('screen_name'),
        ]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('app-show', 'Could not find user'));
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
            $this->getData($user, $filter),
        ));
    }

    /**
     * @return array{
     *   data: array<string, array<string, array{win: int, lose: int, kill: int|null, death: int|null, kd_battle: int}>>,
     *   mapNames: array<string, string>,
     *   ruleNames: array<string, string>
     * }
     */
    private function getData(User $user, Battle2FilterForm $filter): array
    {
        $query = $user->getBattle2s()
            ->orderBy(null)
            ->applyFilter($filter)
            ->innerJoinWith(['map', 'rule', 'lobby'], false)
            ->andWhere([
                '{{battle2}}.[[is_win]]' => [true, false],
                '{{lobby2}}.[[key]]' => ['standard', 'fest_normal'],
            ])
            ->select([
                'map_key' => 'MAX({{map2}}.[[key]])',
                'rule_key' => 'MAX({{rule2}}.[[key]])',
                'result' => sprintf('(CASE %s END)', implode(' ', [
                    "WHEN {{battle2}}.[[is_win]] = TRUE THEN 'win'",
                    "ELSE 'lose'",
                ])),
                'count' => 'COUNT(*)',
                'total_kill' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle2}}.[[kill]]',
                ])),
                'total_death' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[death]] IS NULL THEN 0',
                    'ELSE {{battle2}}.[[death]]',
                ])),
                'have_kill_death' => sprintf('SUM(CASE %s END)', implode(' ', [
                    'WHEN {{battle2}}.[[kill]] IS NULL THEN 0',
                    'WHEN {{battle2}}.[[death]] IS NULL THEN 0',
                    'ELSE 1',
                ])),
            ])
            ->groupBy([
                '{{battle2}}.[[map_id]]',
                '{{battle2}}.[[rule_id]]',
                '{{battle2}}.[[is_win]]',
            ]);

        $maps = Map2::sort(Map2::find()->all());
        $rules = Rule2::find()->orderBy(['id' => SORT_ASC])->all();

        $empty = [
            'win' => 0,
            'lose' => 0,
            'kill' => null,
            'death' => null,
            'kd_battle' => 0,
        ];

        $ret = ['total' => []];
        foreach ($rules as $rule) {
            $ret['total'][$rule->key] = $empty;
        }
        foreach ($maps as $map) {
            $tmp = [];
            foreach ($rules as $rule) {
                $tmp[$rule->key] = $empty;
            }
            $ret[$map->key] = $tmp;
        }

        foreach ($query->createCommand()->queryAll() as $row) {
            $row = (object)$row;
            $mapKey = (string)$row->map_key;
            $ruleKey = (string)$row->rule_key;
            $result = $row->result === 'win' ? 'win' : 'lose';
            $count = (int)$row->count;
            $kill = (int)$row->total_kill;
            $death = (int)$row->total_death;
            $kdBattle = (int)$row->have_kill_death;

            $cell = $ret[$mapKey][$ruleKey] ?? $empty;
            $cell[$result] = $count;
            $cell['kill'] = (int)$cell['kill'] + $kill;
            $cell['death'] = (int)$cell['death'] + $death;
            $cell['kd_battle'] += $kdBattle;
            $ret[$mapKey][$ruleKey] = $cell;

            $sumTargets = substr($mapKey, 0, 8) === 'mystery_' ? ['total', 'mystery'] : ['total'];
            foreach ($sumTargets as $target) {
                $cell = $ret[$target][$ruleKey] ?? $empty;
                $cell[$result] += $count;
                $cell['kill'] = (int)$cell['kill'] + $kill;
                $cell['death'] = (int)$cell['death'] + $death;
                $cell['kd_battle'] += $kdBattle;
                $ret[$target][$ruleKey] = $cell;
            }
        }

        $maps2 = ArrayHelper::map($maps, 'key', fn (Map2 $map): string => Yii::t('app-map2', $map->name));

        $rules2 = ArrayHelper::map(
            $rules,
            'key',
            fn (Rule2 $rule): string => Yii::t('app-rule2', $rule->name),
        );

        return [
            'data' => $ret,
            'mapNames' => $maps2,
            'ruleNames' => $rules2,
        ];
    }
}

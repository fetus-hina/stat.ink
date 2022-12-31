<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show\v2;

use Yii;
use app\models\Battle2;
use app\models\Battle2FilterForm;
use app\models\User;
use app\models\Weapon2;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction;

class UserStatByWeaponAction extends ViewAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne([
            'screen_name' => $request->get('screen_name'),
        ]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $filter = new Battle2FilterForm();
        $filter->load($_GET);
        $filter->screen_name = $user->screen_name;
        $filter->validate();

        return $this->controller->render('user-stat-by-weapon', [
            'user' => $user,
            'list' => $this->getList($user, $filter),
            'filter' => $filter,
        ]);
    }

    public function getList(User $user, Battle2FilterForm $filter): array
    {
        // {{{
        $kill = sprintf('(CASE %s END)', implode(' ', [
            'WHEN {{battle2}}.[[kill]] IS NULL THEN NULL',
            'WHEN {{battle2}}.[[death]] IS NULL THEN NULL',
            'ELSE {{battle2}}.[[kill]]',
        ]));

        $death = sprintf('(CASE %s END)', implode(' ', [
            'WHEN {{battle2}}.[[kill]] IS NULL THEN NULL',
            'WHEN {{battle2}}.[[death]] IS NULL THEN NULL',
            'ELSE {{battle2}}.[[death]]',
        ]));

        $mkColumns = function (string $name, string $param): array {
            return [
                "min_{$name}" => "MIN({$param})",
                "p5_{$name}"  => "PERCENTILE_CONT(0.05) WITHIN GROUP (ORDER BY {$param})",
                "q1_{$name}"  => "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY {$param})",
                "med_{$name}" => "PERCENTILE_CONT(0.50) WITHIN GROUP (ORDER BY {$param})",
                "q3_{$name}"  => "PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY {$param})",
                "p95_{$name}" => "PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY {$param})",
                "max_{$name}" => "MAX({$param})",
                "avg_{$name}" => "AVG({$param})",
                "sd_{$name}"  => "STDDEV_POP({$param})",
                "mod_{$name}" => "mode() WITHIN GROUP (ORDER BY {$param})",
            ];
        };

        $query = Battle2::find() // {{{
            ->innerJoinWith([
                'weapon',
                'weapon.subweapon',
                'weapon.special',
            ], false)
            ->andWhere(['and',
                ['{{battle2}}.[[user_id]]' => $user->id],
                ['not', ['{{battle2}}.[[weapon_id]]' => null]],
            ])
            ->groupBy(['{{battle2}}.[[weapon_id]]'])
            ->orderBy([
                'COUNT(*)' => SORT_DESC,
                'win_rate' => SORT_DESC,
                '{{battle2}}.[[weapon_id]]' => SORT_DESC,
            ])
            ->select(array_merge(
                [
                    'weapon_id' => '{{battle2}}.[[weapon_id]]',
                    'weapon_key' => 'MAX({{weapon2}}.[[key]])',
                    'weapon_name' => 'MAX({{weapon2}}.[[name]])',
                    'subweapon_key' => 'MAX({{subweapon2}}.[[key]])',
                    'subweapon_name' => 'MAX({{subweapon2}}.[[name]])',
                    'special_key' => 'MAX({{special2}}.[[key]])',
                    'special_name' => 'MAX({{special2}}.[[name]])',
                    'battles' => 'COUNT(*)',
                    'win_rate' => sprintf(
                        '(%s::double precision / NULLIF(%s::double precision, 0.0))',
                        'SUM(CASE WHEN {{battle2}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)',
                        'SUM(CASE WHEN {{battle2}}.[[is_win]] IS NOT NULL THEN 1 ELSE 0 END)',
                    ),
                ],
                $mkColumns('kill', $kill),
                $mkColumns('death', $death),
                $mkColumns('ka', '{{battle2}}.[[kill_or_assist]]'),
                $mkColumns('sp', '{{battle2}}.[[special]]'),
            ));
        // }}}

        if ($filter && !$filter->hasErrors()) {
            $query->applyFilter($filter);
        }

        $list = array_map(
            function (array $row): array {
                foreach ($row as $key => $value) {
                    if ($value === null) {
                        continue;
                    }
                    if ($key === 'weapon_id' || $key === 'battles') {
                        $row[$key] = (int)$value;
                    } elseif ($key === 'win_rate') {
                        $row[$key] = (float)$value;
                    } elseif ($key === 'weapon_name') {
                        $row[$key] = Yii::t('app-weapon2', $row['weapon_name']);
                    } elseif ($key === 'subweapon_name') {
                        $row[$key] = Yii::t('app-subweapon2', $row['subweapon_name']);
                    } elseif ($key === 'special_name') {
                        $row[$key] = Yii::t('app-special2', $row['special_name']);
                    } else {
                        switch (substr($key, 0, 4)) {
                            case 'min_':
                            case 'max_':
                                $row[$key] = (int)$value;
                                break;

                            case 'avg_':
                            case 'med_':
                            case 'mod_':
                                $row[$key] = (float)$value;
                                break;
                        }
                    }
                }
                return $row;
            },
            $query->createCommand()->queryAll(),
        );
        return $list;
        // }}}
    }
}

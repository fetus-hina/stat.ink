<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show\v2;

use Yii;
use app\models\Battle2;
use app\models\Battle2FilterForm;
use app\models\User;
use app\models\Weapon2;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

class UserStatByWeaponAction extends BaseAction
{
    public function run()
    {
        $request = Yii::$app->getRequest();
        $user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$user) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }

        $filter = new Battle2FilterForm();
        $filter->load($_GET);
        $filter->validate();

        return $this->controller->render('user-stat-by-weapon', [
            'user' => $user,
            'list' => $this->getList($user, $filter),
            'filter' => $filter,
        ]);
    }

    public function getList(User $user, Battle2FilterForm $filter)
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

        $query = Battle2::find() // {{{
            ->innerJoinWith(['weapon'], false)
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
            ->select([
                'weapon_id'     => '{{battle2}}.[[weapon_id]]',
                'weapon_key'    => 'MAX({{weapon2}}.[[key]])',
                'weapon_name'   => 'MAX({{weapon2}}.[[name]])',
                'battles'       => 'COUNT(*)',
                'win_rate'      => sprintf(
                    '(%s::double precision / NULLIF(%s::double precision, 0.0))',
                    'SUM(CASE WHEN {{battle2}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)',
                    'SUM(CASE WHEN {{battle2}}.[[is_win]] IS NOT NULL THEN 1 ELSE 0 END)'
                ),
                'avg_kill'      => "AVG({$kill})",
                'min_kill'      => "MIN({$kill})",
                'max_kill'      => "MAX({$kill})",
                'med_kill'      => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$kill})",
                'mod_kill'      => "MODE() WITHIN GROUP (ORDER BY {$kill})",
                'avg_death'     => "AVG({$death})",
                'min_death'     => "MIN({$death})",
                'max_death'     => "MAX({$death})",
                'med_death'     => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$death})",
                'mod_death'     => "MODE() WITHIN GROUP (ORDER BY {$death})",
                'avg_ka'        => "AVG({{battle2}}.[[kill_or_assist]])",
                'min_ka'        => "MIN({{battle2}}.[[kill_or_assist]])",
                'max_ka'        => "MAX({{battle2}}.[[kill_or_assist]])",
                'med_ka'        => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[kill_or_assist]])",
                'mod_ka'        => "MODE() WITHIN GROUP (ORDER BY {{battle2}}.[[kill_or_assist]])",
                'avg_sp'        => "AVG({{battle2}}.[[special]])",
                'min_sp'        => "MIN({{battle2}}.[[special]])",
                'max_sp'        => "MAX({{battle2}}.[[special]])",
                'med_sp'        => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])",
                'mod_sp'        => "MODE() WITHIN GROUP (ORDER BY {{battle2}}.[[special]])",
            ]);
        // }}}

        if ($filter && !$filter->hasErrors()) {
            $query->applyFilter($filter);
        }
        
        $list = array_map(
            function (array $row) : array {
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
            $query->createCommand()->queryAll()
        );
        return $list;
        // }}}
    }
}

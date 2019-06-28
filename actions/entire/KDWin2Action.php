<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire;

use Yii;
use app\models\Battle2FilterForm;
use app\models\Map2;
use app\models\StatWeapon2Result;
use app\models\WeaponType2;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction as BaseAction;

class KDWin2Action extends BaseAction
{
    const KD_LIMIT = 16;

    public function run()
    {
        $filter = new Battle2FilterForm();
        $filter->load($_GET) && $filter->validate();

        return $this->controller->render('kd-win2', [
            'data' => $this->makeData($filter),
            'filter' => $filter,
        ]);
    }

    private function makeData($filter) : array
    {
        $query = StatWeapon2Result::find()
            ->asArray()
            ->innerJoinWith('rule', false)
            ->applyFilter($filter)
            ->select([
              'rule'    => 'MAX(rule2.key)',
              'kill'    => 'stat_weapon2_result.kill',
              'death'   => 'stat_weapon2_result.death',
              'battle'  => 'SUM(stat_weapon2_result.battles)',
              'win'     => 'SUM(stat_weapon2_result.wins)',
            ])
            ->groupBy([
                'stat_weapon2_result.rule_id',
                'stat_weapon2_result.kill',
                'stat_weapon2_result.death',
            ]);
        $result = [];
        foreach ($query->all() as $row) {
            $rule = $row['rule'];
            $k = min(static::KD_LIMIT, (int)$row['kill']);
            $d = min(static::KD_LIMIT, (int)$row['death']);

            if (!isset($result[$rule])) {
                $result[$rule] = [];
            }
            if (!isset($result[$rule][$k])) {
                $result[$rule][$k] = [];
            }
            if (!isset($result[$rule][$k][$d])) {
                $result[$rule][$k][$d] = [
                    'battle' => 0,
                    'win' => 0,
                ];
            }
            $result[$rule][$k][$d]['battle'] += (int)$row['battle'];
            $result[$rule][$k][$d]['win'] += (int)$row['win'];
        }
        return $result;
    }
}

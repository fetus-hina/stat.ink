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
use app\models\GameMode;

class KDWinAction extends BaseAction
{
    public function run()
    {
        $data = [];
        $modes = GameMode::find()->orderBy('id')->all();
        foreach ($modes as $mode) {
            $tmpData = [];
            foreach ($mode->rules as $rule) {
                $tmpData[] = (object)[
                    'key' => $rule->key,
                    'name' => Yii::t('app-rule', $rule->name),
                    'data' => $this->makeData($rule),
                ];
            }
            usort($tmpData, function ($a, $b) {
                return strcmp($a->name, $b->name);
            });
            $data = array_merge($data, $tmpData);
        }

        return $this->controller->render('kd-win.tpl', [
            'rules' => $data,
        ]);
    }

    private function makeData(Rule $rule)
    {
        $ret = [];
        foreach (range(0, 16) as $i) {
            $tmp = [];
            foreach (range(0, 16) as $j) {
                $tmp[] = (object)[
                    'battle' => 0,
                    'win' => 0,
                ];
            }
            $ret[] = $tmp;
        }

        $maxBattle = 0;
        foreach ($this->query($rule) as $row) {
            $i = $row['kill'] > 15 ? 16 : $row['kill'];
            $j = $row['death'] > 15 ? 16 : $row['death'];
            $ret[$i][$j]->battle += $row['count'];
            $ret[$i][$j]->win += $row['win'];
        }

        return $ret;
    }

    private function query(Rule $rule)
    {
        $query = (new \yii\db\Query())
            ->select([
                'kill' => '{{battle}}.[[kill]]',
                'death' => '{{battle}}.[[death]]',
                'count' => 'COUNT(*)',
                'win' => 'SUM(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)',
            ])
            ->from('battle')
            ->leftJoin('lobby', '{{battle}}.[[lobby_id]] = {{lobby}}.[[id]]')
            ->andWhere(['or', 
                ['{{battle}}.[[lobby_id]]' => null],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->andWhere('{{battle}}.[[is_win]] IS NOT NULL')
            ->andWhere('{{battle}}.[[kill]] IS NOT NULL')
            ->andWhere('{{battle}}.[[death]] IS NOT NULL')
            ->andWhere(['{{battle}}.[[rule_id]]' => $rule->id])
            ->groupBy(['{{battle}}.[[kill]]', '{{battle}}.[[death]]']);
        return $query->createCommand()->queryAll();
    }
}

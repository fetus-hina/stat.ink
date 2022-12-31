<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire\users;

use app\models\StatEntireUser;
use yii\db\Query;

trait Splatoon1
{
    protected function getPostStats()
    {
        $lastSummariedDate = null;
        if ($stats = $this->getPostStatsSummarized()) {
            $lastSummariedDate = $stats[count($stats) - 1]->date;
        } else {
            $stats = [];
        }

        $query = (new Query())
            ->select([
                'date' => '{{battle}}.[[at]]::date',
                'battle_count' => 'COUNT({{battle}}.*)',
                'user_count' => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
            ])
            ->from('battle')
            ->groupBy('{{battle}}.[[at]]::date')
            ->orderBy('{{battle}}.[[at]]::date ASC');
        if ($lastSummariedDate !== null) {
            $query->andWhere(['>', '{{battle}}.[[at]]', $lastSummariedDate . ' 23:59:59']);
        }

        foreach ($query->createCommand()->queryAll() as $row) {
            $stats[] = (object)$row;
        }

        return array_map(
            function ($a) {
                return [
                    'date' => $a->date,
                    'battle' => $a->battle_count,
                    'user' => $a->user_count,
                ];
            },
            $stats,
        );
    }

    private function getPostStatsSummarized()
    {
        return StatEntireUser::find()
            ->orderBy('{{stat_entire_user}}.[[date]] ASC')
            ->all();
    }

    protected function getAgentStats()
    {
        $list = $this->queryAgentStats();
        $agents = $this->queryAgentDetails(array_map(
            function ($a) {
                return $a['agent_id'];
            },
            $list,
        ));
        $t = @$_SERVER['REQUEST_TIME'] ?: time();
        foreach ($list as &$row) {
            $agent = @$agents[$row['agent_id']] ?: null;
            $row['agent_name'] = $agent->name ?? '';
            $row['agent_version'] = $agent->version ?? '';
            $row['agent_is_old'] = $agent->getIsOldIkalogAsAtTheTime($t) ?? false;
            $row['agent_prod_url'] = $agent->productUrl ?? '';
            $row['agent_rev_url'] = $agent->versionUrl ?? '';
            unset($row);
        }
        return $list;
    }

    private function queryAgentStats()
    {
        $t2 = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $t1 = gmmktime(
            gmdate('H', $t2),
            gmdate('i', $t2),
            gmdate('s', $t2) + 1,
            gmdate('n', $t2),
            gmdate('j', $t2) - 1,
            gmdate('Y', $t2),
        );
        $query = (new Query())
            ->select([
                'agent_id',
                'battle' => 'COUNT(*)',
                'user' => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
            ])
            ->from('battle')
            ->innerJoin('agent', '{{battle}}.[[agent_id]] = {{agent}}.[[id]]')
            ->andWhere(['between', '{{battle}}.[[at]]',
                gmdate('Y-m-d\TH:i:sP', $t1),
                gmdate('Y-m-d\TH:i:sP', $t2)])
            ->groupBy('{{battle}}.[[agent_id]]')
            ->orderBy(implode(', ', [
                '[[battle]] DESC',
                '[[user]] DESC',
                '[[agent_id]] DESC',
            ]));
        return $query->createCommand()->queryAll();
    }
}

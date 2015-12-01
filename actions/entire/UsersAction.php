<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\entire;

use Yii;
use yii\web\ViewAction as BaseAction;
use app\models\Agent;

class UsersAction extends BaseAction
{
    public function run()
    {
        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        return $this->controller->render('users.tpl', [
            'posts' => $this->postStats,
            'agents' => $this->agentStats,
        ]);
    }

    public function getPostStats()
    {
        $query = (new \yii\db\Query())
            ->select([
                'date'      => '{{battle}}.[[at]]::date',
                'battle'    => 'COUNT({{battle}}.*)',
                'user'      => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
            ])
            ->from('battle')
            ->groupBy('{{battle}}.[[at]]::date')
            ->orderBy('{{battle}}.[[at]]::date ASC');
        return $query->createCommand()->queryAll();
    }

    public function getAgentStats()
    {
        $list = $this->queryAgentStats();
        $agents = $this->queryAgentDetails(array_map(
            function($a) {
                return $a['agent_id'];
            },
            $list
        ));
        $t = @$_SERVER['REQUEST_TIME'] ?: time();
        foreach ($list as &$row) {
            $agent = @$agents[$row['agent_id']] ?: null;
            $row['agent_name'] = $agent ? $agent->name : '';
            $row['agent_version'] = $agent ? $agent->version : '';
            $row['agent_is_old'] = $agent ? $agent->getIsOldIkalogAsAtTheTime($t) : false;
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
            gmdate('Y', $t2)
        );
        $query = (new \yii\db\Query())
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

    private function queryAgentDetails(array $idList)
    {
        $ret = [];
        foreach (Agent::findAll(['id' => $idList]) as $agent) {
            $ret[$agent->id] = $agent;
        }
        return $ret;
    }
}

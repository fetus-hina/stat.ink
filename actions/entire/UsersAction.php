<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire;

use Yii;
use app\actions\entire\users\Splatoon1;
use app\actions\entire\users\Splatoon2;
use app\actions\entire\users\Splatoon3;
use app\models\Agent;
use app\models\AgentGroup;
use app\models\StatAgentUser;
use yii\base\Action;
use yii\db\Query;
use yii\web\Controller;

use function array_map;
use function assert;
use function usort;

class UsersAction extends Action
{
    use Splatoon1;
    use Splatoon2;
    use Splatoon3;

    public function run()
    {
        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        $c = $this->controller;
        assert($c instanceof Controller);

        return $c->render('users', [
            'agentNames' => $this->getAgentNames(),
            'agents' => $this->getAgentStats(),
            'agents2' => $this->getAgentStats2(),
            'agents3' => $this->getAgentStats3(),
            'combineds' => $this->getCombineds(),
            'posts' => $this->getPostStats(),
            'posts2' => $this->getPostStats2(),
            'posts3' => $this->getPostStats3(),
        ]);
    }

    private function queryAgentDetails(array $idList)
    {
        $ret = [];
        foreach (Agent::findAll(['id' => $idList]) as $agent) {
            $ret[$agent->id] = $agent;
        }
        return $ret;
    }

    protected function getAgentNames()
    {
        $query = (new Query())
            ->select(['agent'])
            ->from(StatAgentUser::tableName())
            ->groupBy('agent');
        $list = array_map(
            fn ($a) => $a['agent'],
            $query->createCommand()->queryAll(),
        );
        usort($list, 'strnatcasecmp');
        return $list;
    }

    protected function getCombineds(): array
    {
        $list = array_map(
            fn (array $a): string => $a['name'] ?? '',
            AgentGroup::find()->asArray()->all(),
        );
        usort($list, 'strnatcasecmp');
        return $list;
    }
}

<?php

/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\models\Agent;
use app\models\AgentGroup;
use app\models\Battle2;
use app\models\StatAgentUser;
use app\models\StatEntireUser;
use app\models\StatEntireUser2;
use yii\db\Query;
use yii\web\ViewAction as BaseAction;

use const SORT_ASC;

class UsersAction extends BaseAction
{
    public function run()
    {
        Yii::$app->db
            ->createCommand("SET timezone TO 'UTC-6'")
            ->execute();

        return $this->controller->render('users', [
            'posts' => $this->postStats,
            'posts2' => $this->postStats2,
            'agents' => $this->agentStats,
            'agentNames' => $this->agentNames,
            'combineds' => $this->combineds,
            'agents2' => $this->agentStats2,
        ]);
    }

    public function getPostStats()
    {
        $lastSummariedDate = null;
        if ($stats = $this->getPostStatsSummarized()) {
            $lastSummariedDate = $stats[count($stats) - 1]->date;
        } else {
            $stats = [];
        }

        $query = (new Query())
            ->select([
                'date'          => '{{battle}}.[[at]]::date',
                'battle_count'  => 'COUNT({{battle}}.*)',
                'user_count'    => 'COUNT(DISTINCT {{battle}}.[[user_id]])',
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
            fn ($a) => [
                'date' => $a->date,
                'battle' => $a->battle_count,
                'user' => $a->user_count,
            ],
            $stats
        );
    }

    private function getPostStatsSummarized()
    {
        return StatEntireUser::find()
            ->orderBy('{{stat_entire_user}}.[[date]] ASC')
            ->all();
    }

    public function getPostStats2()
    {
        $lastSummariedDate = null;
        if ($stats = $this->getPostStatsSummarized2()) {
            $lastSummariedDate = $stats[count($stats) - 1]['date'];
        } else {
            $stats = [];
        }

        $query = (new Query())
            ->select([
                'date'          => '{{battle2}}.[[created_at]]::date',
                'battle_count'  => 'COUNT({{battle2}}.*)',
                'user_count'    => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
            ])
            ->from('battle2')
            ->groupBy('{{battle2}}.[[created_at]]::date')
            ->orderBy(['date' => SORT_ASC]);
        if ($lastSummariedDate !== null) {
            $query->andWhere(['>', '{{battle2}}.[[created_at]]', $lastSummariedDate . ' 23:59:59']);
        }

        foreach ($query->createCommand()->queryAll() as $row) {
            $stats[] = $row;
        }

        return array_map(
            fn ($a) => [
                'date' => $a['date'],
                'battle' => $a['battle_count'],
                'user' => $a['user_count'],
            ],
            $stats
        );
    }

    private function getPostStatsSummarized2(): array
    {
        return StatEntireUser2::find()
            ->orderBy(['date' => SORT_ASC])
            ->asArray()
            ->all();
    }

    public function getAgentStats()
    {
        $list = $this->queryAgentStats();
        $agents = $this->queryAgentDetails(array_map(
            fn ($a) => $a['agent_id'],
            $list
        ));
        $t = @$_SERVER['REQUEST_TIME'] ?: time();
        foreach ($list as &$row) {
            $agent = @$agents[$row['agent_id']] ?: null;
            $row['agent_name']      = $agent->name ?? '';
            $row['agent_version']   = $agent->version ?? '';
            $row['agent_is_old']    = $agent->getIsOldIkalogAsAtTheTime($t) ?? false;
            $row['agent_prod_url']  = $agent->productUrl ?? '';
            $row['agent_rev_url']   = $agent->versionUrl ?? '';
            unset($row);
        }
        return $list;
    }

    private function queryAgentStats()
    {
        $t2 = $_SERVER['REQUEST_TIME'] ?? time();
        $t1 = gmmktime(
            gmdate('H', $t2),
            gmdate('i', $t2),
            gmdate('s', $t2) + 1,
            gmdate('n', $t2),
            gmdate('j', $t2) - 1,
            gmdate('Y', $t2)
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
                gmdate('Y-m-d\TH:i:sP', $t2),
            ])
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

    public function getAgentNames()
    {
        $query = (new Query())
            ->select(['agent'])
            ->from(StatAgentUser::tableName())
            ->groupBy('agent');
        $list = array_map(
            fn ($a) => $a['agent'],
            $query->createCommand()->queryAll()
        );
        usort($list, 'strnatcasecmp');
        return $list;
    }

    public function getCombineds()
    {
        $list = array_map(
            fn ($a) => $a['name'],
            AgentGroup::find()->asArray()->all()
        );
        usort($list, 'strnatcasecmp');
        return $list;
    }

    public function getAgentStats2(): array
    {
        $endAt = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
        $startAt = $endAt->sub(new DateInterval('PT24H'));
        $list = Battle2::find()
            ->innerJoinWith(['agent'], false)
            ->where(['and',
                ['>=', '{{battle2}}.[[created_at]]', $startAt->format(DateTime::ATOM)],
                ['<', '{{battle2}}.[[created_at]]', $endAt->format(DateTime::ATOM)],
            ])
            ->select([
                'name'      => '{{agent}}.[[name]]',
                'battles'   => 'COUNT(*)',
                'users'     => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
                'min_id'    => 'MIN({{battle2}}.[[id]])',
                'max_id'    => 'MAX({{battle2}}.[[id]])',
            ])
            ->groupBy(['{{agent}}.[[name]]'])
            ->asArray()
            ->all();
        usort($list, function (array $a, array $b): int {
            foreach (['battles', 'users', 'min_id'] as $key) {
                if ($a[$key] != $b[$key]) {
                    return $b[$key] <=> $a[$key];
                }
            }
            return 0;
        });
        return [
            'term' => [
                's' => DateTimeFormatter::unixTimeToJsonArray($startAt->getTimestamp()),
                'e' => DateTimeFormatter::unixTimeToJsonArray($endAt->getTimestamp() - 1),
            ],
            'agents' => array_map(
                fn (array $row): array => [
                    'name' => (string)$row['name'],
                    'battles' => (int)$row['battles'],
                    'users' => (int)$row['users'],
                    'versions' => $this->getAgentVersion2(
                        $row['name'],
                        $startAt,
                        $endAt,
                        (int)$row['min_id'],
                        (int)$row['max_id']
                    ),
                ],
                $list
            ),
        ];
    }

    private function getAgentVersion2(
        string $name,
        DateTimeImmutable $startAt,
        DateTimeImmutable $endAt,
        int $minId,
        int $maxId
    ): array {
        $versions = Battle2::find()
            ->innerJoinWith(['agent'], false)
            ->where(['and',
                ['{{agent}}.[[name]]' => $name],
                ['between', '{{battle2}}.[[id]]', $minId, $maxId],
                ['>=', '{{battle2}}.[[created_at]]', $startAt->format(DateTime::ATOM)],
                ['<', '{{battle2}}.[[created_at]]', $endAt->format(DateTime::ATOM)],
            ])
            ->select([
                'version' => 'MAX({{agent}}.[[version]])',
                'battles' => 'COUNT(*)',
                'users' => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
            ])
            ->groupBy(['{{battle2}}.[[agent_id]]'])
            ->asArray()
            ->all();
        usort($versions, fn (array $a, array $b): int => version_compare($b['version'], $a['version']));
        return array_map(
            fn (array $row): array => [
                'version' => (string)$row['version'],
                'battles' => (int)$row['battles'],
                'users' => (int)$row['users'],
            ],
            $versions
        );
    }
}

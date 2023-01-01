<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\actions\entire\users;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\components\helpers\DateTimeFormatter;
use app\models\Battle2;
use app\models\StatEntireUser2;
use yii\db\Query;

use function array_map;
use function count;
use function time;
use function usort;
use function version_compare;

use const SORT_ASC;

trait Splatoon2
{
    protected function getPostStats2()
    {
        $lastSummariedDate = null;
        if ($stats = $this->getPostStatsSummarized2()) {
            $lastSummariedDate = $stats[count($stats) - 1]['date'];
        } else {
            $stats = [];
        }

        $query = (new Query())
            ->select([
                'date' => '{{battle2}}.[[created_at]]::date',
                'battle_count' => 'COUNT({{battle2}}.*)',
                'user_count' => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
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
            $stats,
        );
    }

    private function getPostStatsSummarized2(): array
    {
        return StatEntireUser2::find()
            ->orderBy(['date' => SORT_ASC])
            ->asArray()
            ->all();
    }

    protected function getAgentStats2(): array
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
                'name' => '{{agent}}.[[name]]',
                'battles' => 'COUNT(*)',
                'users' => 'COUNT(DISTINCT {{battle2}}.[[user_id]])',
                'min_id' => 'MIN({{battle2}}.[[id]])',
                'max_id' => 'MAX({{battle2}}.[[id]])',
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
                            (int)$row['max_id'],
                        ),
                    ],
                $list,
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
            $versions,
        );
    }
}

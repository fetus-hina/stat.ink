<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
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
use app\components\helpers\Resource;
use app\models\Battle3;
use app\models\StatEntireUser3;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function array_map;
use function assert;
use function count;
use function sprintf;
use function time;
use function usort;
use function version_compare;

use const SORT_ASC;

trait Splatoon3
{
    protected function getPostStats3(): array
    {
        return Yii::$app->db->transaction(
            function (Connection $db): array {
                $db->createCommand("SET LOCAL TIMEZONE TO 'Etc/UTC'")->execute();

                $lastSummariedDate = null;
                if ($stats = $this->getPostStatsSummarized3($db)) {
                    $lastSummariedDate = $stats[count($stats) - 1]['date'];
                } else {
                    $stats = [];
                }

                $query = (new Query())
                    ->select([
                        'date' => '{{%battle3}}.[[created_at]]::date',
                        'battles' => 'COUNT({{%battle3}}.*)',
                        'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                    ])
                    ->from('{{%battle3}}')
                    ->andWhere(['{{%battle3}}.[[is_deleted]]' => false])
                    ->groupBy('{{%battle3}}.[[created_at]]::date')
                    ->orderBy(['date' => SORT_ASC]);

                if ($lastSummariedDate) {
                    $query->andWhere([
                        '>=',
                        '{{%battle3}}.[[created_at]]',
                        $lastSummariedDate . 'T00:00:00+00:00',
                    ]);
                }

                foreach ($query->all($db) as $row) {
                    $stats[] = $row;
                }

                return ArrayHelper::getColumn(
                    $stats,
                    fn (StatEntireUser3|array $model): array => [
                        'date' => ArrayHelper::getValue($model, 'date'),
                        'battle' => ArrayHelper::getValue($model, 'battles'),
                        'user' => ArrayHelper::getValue($model, 'users'),
                    ],
                );
            },
            Transaction::REPEATABLE_READ,
        );
    }

    /**
     * @return StatEntireUser3[]
     */
    private function getPostStatsSummarized3(Connection $db): array
    {
        return StatEntireUser3::find()
            ->orderBy(['date' => SORT_ASC])
            ->all($db);
    }

    protected function getAgentStats3(): array
    {
        $tz = $this->utc3();
        try {
            $endAt = (new DateTimeImmutable())
                ->setTimeZone(new DateTimeZone('Etc/UTC'))
                ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
            $startAt = $endAt->sub(new DateInterval('PT24H'));
            $list = Battle3::find()
                ->innerJoinWith(['agent'], false)
                ->where(['and',
                    ['{{%battle3}}.[[is_deleted]]' => false],
                    ['>=', '{{%battle3}}.[[created_at]]', $startAt->format(DateTime::ATOM)],
                    ['<', '{{%battle3}}.[[created_at]]', $endAt->format(DateTime::ATOM)],
                ])
                ->select([
                    'name' => '{{agent}}.[[name]]',
                    'battles' => 'COUNT(*)',
                    'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                    'min_id' => 'MIN({{%battle3}}.[[id]])',
                    'max_id' => 'MAX({{%battle3}}.[[id]])',
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
                        'versions' => $this->getAgentVersion3(
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
        } finally {
            unset($tz);
        }
    }

    private function getAgentVersion3(
        string $name,
        DateTimeImmutable $startAt,
        DateTimeImmutable $endAt,
        int $minId,
        int $maxId,
    ): array {
        $tz = $this->utc3();
        try {
            $versions = Battle3::find()
                ->innerJoinWith(['agent'], false)
                ->where(['and',
                    [
                        '{{agent}}.[[name]]' => $name,
                        '{{%battle3}}.[[is_deleted]]' => false,
                    ],
                    ['between', '{{%battle3}}.[[id]]', $minId, $maxId],
                    ['>=', '{{%battle3}}.[[created_at]]', $startAt->format(DateTime::ATOM)],
                    ['<', '{{%battle3}}.[[created_at]]', $endAt->format(DateTime::ATOM)],
                ])
                ->select([
                    'version' => 'MAX({{agent}}.[[version]])',
                    'battles' => 'COUNT(*)',
                    'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
                ])
                ->groupBy(['{{%battle3}}.[[agent_id]]'])
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
        } finally {
            unset($tz);
        }
    }

    private function utc3(): Resource
    {
        $conn = Yii::$app->db;
        assert($conn instanceof Connection);

        $oldTZ = $conn->createCommand("SELECT CURRENT_SETTING('TIMEZONE')")->queryScalar();
        $conn->createCommand("SET TIMEZONE TO 'Etc/UTC'")->execute();

        return new Resource($oldTZ, function (string $oldTZ) use ($conn): void {
            $conn->createCommand(sprintf('SET TIMEZONE TO %s', $conn->quoteValue($oldTZ)))
                ->execute();
        });
    }
}

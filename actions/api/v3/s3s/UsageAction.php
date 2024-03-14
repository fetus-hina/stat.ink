<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\api\v3\s3s;

use Yii;
use app\actions\api\v3\traits\ApiInitializerTrait;
use yii\base\Action;
use yii\db\Connection;
use yii\db\Expression;
use yii\db\Query;

use function array_values;
use function assert;
use function gmdate;
use function strcmp;
use function time;
use function usort;
use function version_compare;
use function vsprintf;

final class UsageAction extends Action
{
    use ApiInitializerTrait;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->apiInit();
    }

    /**
     * @return array[]
     */
    public function run()
    {
        $conn = Yii::$app->db;
        assert($conn instanceof Connection);
        $conn->createCommand("SET TIMEZONE TO 'Etc/UTC'")->execute();

        $today = gmdate('Y-m-d', $_SERVER['REQUEST_TIME'] ?? time());
        $query = (new Query())
            ->select([
                'date' => '{{%battle3}}.[[created_at]]::date',
                'version' => 'MAX({{%agent}}.[[version]])',
                'battles' => 'COUNT(*)',
                'users' => 'COUNT(DISTINCT {{%battle3}}.[[user_id]])',
            ])
            ->from('{{%battle3}}')
            ->innerJoin('{{%agent}}', '{{%battle3}}.[[agent_id]] = {{%agent}}.[[id]]')
            ->andWhere([
                '{{%battle3}}.[[is_deleted]]' => false,
                '{{%agent}}.[[name]]' => 's3s',
            ])
            ->andWhere([
                '>=',
                '{{%battle3}}.[[created_at]]::date',
                new Expression(
                    vsprintf("%s::date - '30 days'::interval", [
                        $conn->quoteValue($today),
                    ]),
                ),
            ])
            ->groupBy([
                '{{%battle3}}.[[created_at]]::date',
                '{{%battle3}}.[[agent_id]]',
            ]);
        $list = $query->createCommand()->queryAll();
        usort(
            $list,
            fn (array $a, array $b): int => strcmp($a['date'], $b['date'])
                ?: version_compare($a['version'], $b['version'])
                ?: strcmp($a['version'], $b['version']),
        );

        $results = [];
        foreach ($list as $row) {
            $date = $row['date'];
            if (!isset($results[$date])) {
                $results[$date] = [
                    'date' => $date,
                    'versions' => [],
                ];
            }
            $results[$date]['versions'][$row['version']] = [
                'users' => (int)$row['users'],
                'battles' => (int)$row['battles'],
            ];
        }
        return array_values($results);
    }
}

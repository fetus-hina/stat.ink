<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

namespace app\actions\entire\users;

use Yii;
use app\models\StatEntireSalmon3;
use yii\db\Connection;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;

use function count;

use const SORT_ASC;

trait Salmon3
{
    protected function getPostStatsSalmon3(): array
    {
        return Yii::$app->db->transaction(
            function (Connection $db): array {
                $db->createCommand("SET LOCAL TIMEZONE TO 'Etc/UTC'")->execute();

                $lastSummariedDate = null;
                if ($stats = $this->getPostStatsSummarizedSalmon3($db)) {
                    $lastSummariedDate = $stats[count($stats) - 1]['date'];
                } else {
                    $stats = [];
                }

                $query = new Query()
                    ->select([
                        'date' => '{{%salmon3}}.[[created_at]]::date',
                        'jobs' => 'COUNT({{%salmon3}}.*)',
                        'users' => 'COUNT(DISTINCT {{%salmon3}}.[[user_id]])',
                    ])
                    ->from('{{%salmon3}}')
                    ->andWhere(['{{%salmon3}}.[[is_deleted]]' => false])
                    ->groupBy('{{%salmon3}}.[[created_at]]::date')
                    ->orderBy(['date' => SORT_ASC]);

                if ($lastSummariedDate) {
                    $query->andWhere([
                        '>=',
                        '{{%salmon3}}.[[created_at]]',
                        $lastSummariedDate . 'T00:00:00+00:00',
                    ]);
                }

                foreach ($query->all($db) as $row) {
                    $stats[] = $row;
                }

                return ArrayHelper::getColumn(
                    $stats,
                    fn (StatEntireSalmon3|array $model): array => [
                        'date' => ArrayHelper::getValue($model, 'date'),
                        'job' => ArrayHelper::getValue($model, 'jobs'),
                        'user' => ArrayHelper::getValue($model, 'users'),
                    ],
                );
            },
            Transaction::REPEATABLE_READ,
        );
    }

    /**
     * @return StatEntireSalmon3[]
     */
    private function getPostStatsSummarizedSalmon3(Connection $db): array
    {
        return StatEntireSalmon3::find()
            ->orderBy(['date' => SORT_ASC])
            ->all($db);
    }
}

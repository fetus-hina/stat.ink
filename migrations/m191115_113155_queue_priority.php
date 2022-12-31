<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

class m191115_113155_queue_priority extends Migration
{
    public function safeUp()
    {
        if (!$this->hasQueueTable()) {
            return;
        }

        $priorities = $this->getPriorities();
        $sql = vsprintf('UPDATE {{queue}} SET [[priority]] = %s', [
            vsprintf('(CASE %s ELSE 1024 END)', [
                implode(' ', array_map(
                    fn (string $jobName, int $priority): string => vsprintf('WHEN %s THEN %d', [
                            sprintf('[[job]] LIKE %s', Yii::$app->db->quoteValue(
                                '%' . $jobName . '%', // 既知の値しか入らないのでエスケープ省略
                            )),
                            $priority,
                        ]),
                    array_keys($priorities),
                    array_values($priorities),
                )),
            ]),
        ]);
        $this->execute($sql);
    }

    public function safeDown()
    {
        if (!$this->hasQueueTable()) {
            return;
        }

        $this->update('queue', ['priority' => 1024], '1 = 1');
    }

    private function hasQueueTable(): bool
    {
        $q = (new Query())
            ->select(['count' => 'COUNT(*)'])
            ->from('{{information_schema}}.{{tables}}')
            ->andWhere(['and',
                ['{{tables}}.[[table_name]]' => 'queue'],
            ]);
        return $q->scalar() > 0;
    }

    private function getPriorities(): array
    {
        $defautPriority = 1024;
        return [
            'SlackJob' => $defautPriority - 3,
            'OstatusJob' => $defautPriority - 2,
            'UserStatsJob' => $defautPriority - 1,
            'ImageS3Job' => $defautPriority + 1,
        ];
    }
}

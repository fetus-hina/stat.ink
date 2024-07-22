<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\helpers\TypeHelper;
use yii\db\Query;

final class m240722_171610_eggstra_work extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $scheduleId = $this->getScheduleId();
        if (is_int($scheduleId)) {
            $this->insert('{{%eggstra_work_official_result3}}', [
                'schedule_id' => $scheduleId,
                'gold' => 227,
                'silver' => 181,
                'bronze' => 145,
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $scheduleId = $this->getScheduleId();
        if (is_int($scheduleId)) {
            $this->delete('{{%eggstra_work_official_result3}}', ['schedule_id' => $scheduleId]);
        }

        return true;
    }

    private function getScheduleId(): ?int
    {
        return TypeHelper::intOrNull(
            (new Query())
                ->select(['id'])
                ->from('{{%salmon_schedule3}}')
                ->andWhere([
                    'is_eggstra_work' => true,
                    'start_at' => '2024-07-20T00:00:00+00:00',
                ])
                ->limit(1)
                ->scalar(),
        );
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%eggstra_work_official_result3}}',
        ];
    }
}

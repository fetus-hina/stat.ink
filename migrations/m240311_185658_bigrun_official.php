<?php

/**
 * @copyright Copyright (C) 2015-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m240311_185658_bigrun_official extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $id = $this->getScheduleId();
        if (is_int($id)) {
            $this->insert('{{%bigrun_official_border3}}', [
                'schedule_id' => $id,
                'gold' => 135,
                'silver' => 110,
                'bronze' => 85,
            ]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $id = $this->getScheduleId();
        if (is_int($id)) {
            $this->delete('{{%bigrun_official_border3}}', ['schedule_id' => $id]);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%bigrun_official_border3}}',
        ];
    }

    private function getScheduleId(): ?int
    {
        $id = filter_var(
            (new Query())
                ->select(['id'])
                ->from('{{%salmon_schedule3}}')
                ->andWhere(['and',
                    [
                        'start_at' => '2024-03-09T00:00:00+00:00',
                        'map_id' => null,
                    ],
                    ['not', ['big_map_id' => null]],
                ])
                ->limit(1)
                ->scalar(),
            FILTER_VALIDATE_INT,
        );
        return is_int($id) ? $id : null;
    }
}

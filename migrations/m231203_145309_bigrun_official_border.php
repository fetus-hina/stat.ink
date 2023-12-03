<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Query;

final class m231203_145309_bigrun_official_border extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%bigrun_official_border3}}', [
            'schedule_id' => $this->pkRef('{{%salmon_schedule3}}')->notNull(),
            'gold' => $this->integer()->notNull(),
            'silver' => $this->integer()->notNull(),
            'bronze' => $this->integer()->notNull(),

            'PRIMARY KEY ([[schedule_id]])',
        ]);

        $id = (new Query())
            ->select('id')
            ->from('{{%salmon_schedule3}}')
            ->andWhere(['start_at' => '2023-12-02T00:00:00+00:00'])
            ->andWhere(['not', ['big_map_id' => null]])
            ->limit(1)
            ->scalar();

        if ($id) {
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
        $this->dropTable('{{%bigrun_official_border3}}');

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
}

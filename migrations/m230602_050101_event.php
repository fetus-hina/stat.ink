<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230602_050101_event extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%event3}}', [
            'id' => $this->primaryKey(),
            'internal_id' => $this->string(128)->notNull()->unique(),
            'name' => $this->text()->notNull(),
            'desc' => $this->text()->null(),
            'regulation' => $this->text()->null(),
        ]);

        $this->createTable('{{%event_schedule3}}', [
            'id' => $this->primaryKey(),
            'event_id' => $this->pkRef('{{%event3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),
            'start_at' => $this->timestampTZ(0)->notNull(),
            'end_at' => $this->timestampTZ(0)->notNull(),
        ]);
        $this->createIndex('event_schedule3_start_at', '{{%event_schedule3}}', 'start_at');
        $this->createIndex('event_schedule3_end_at', '{{%event_schedule3}}', 'end_at');

        $this->createTable('{{%event_map3}}', [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->pkRef('{{%event_schedule3}}')->notNull(),
            'map_id' => $this->pkRef('{{%map3}}')->notNull(),

            'UNIQUE ([[schedule_id]], [[map_id]])',
        ]);

        $this->createTable('{{%event_period3}}', [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->pkRef('{{%event_schedule3}}')->notNull(),
            'start_at' => $this->timestampTZ(0)->notNull(),
            'end_at' => $this->timestampTZ(0)->notNull(),

            'UNIQUE ([[schedule_id]], [[start_at]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%event_period3}}',
            '{{%event_map3}}',
            '{{%event_schedule3}}',
            '{{%event3}}',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    protected function vacuumTables(): array
    {
        return [
            '{{%event3}}',
            '{{%event_map3}}',
            '{{%event_period3}}',
            '{{%event_schedule3}}',
        ];
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m221001_112140_schedule3 extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%schedule3}}', [
            'id' => $this->primaryKey(),
            'period' => $this->integer()->notNull(),
            'lobby_id' => $this->pkRef('{{%lobby3}}')->notNull(),
            'rule_id' => $this->pkRef('{{%rule3}}')->notNull(),

            'UNIQUE ([[period]], [[lobby_id]])',
        ]);

        $this->createTable('{{%schedule_map3}}', [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->pkRef('{{%schedule3}}')->notNull(),
            'map_id' => $this->pkRef('{{%map3}}')->notNull(),
        ]);
        $this->createIndex('schedule_map3_schedule_id_key', '{{%schedule_map3}}', ['schedule_id']);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables(['{{%schedule_map3}}', '{{%schedule3}}']);

        return true;
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m260609_103759_queue_salmon_export_json3 extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->createTable('{{%queue_salmon_export_json3}}', [
            'id' => 'UUID NOT NULL PRIMARY KEY',
            'user_id' =>
                'INTEGER NOT NULL UNIQUE REFERENCES {{user}}([[id]]) ON DELETE CASCADE',
            'updated_at' => $this->timestampTZ(0)->notNull(),
        ]);

        $this->createIndex(
            'queue_salmon_export_json3_updated_at',
            '{{%queue_salmon_export_json3}}',
            ['updated_at'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropTable('{{%queue_salmon_export_json3}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%queue_salmon_export_json3}}',
        ];
    }
}

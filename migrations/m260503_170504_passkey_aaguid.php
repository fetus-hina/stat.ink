<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m260503_170504_passkey_aaguid extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->createTable('{{%passkey_aaguid}}', [
            'aaguid' => 'UUID NOT NULL',
            'name' => $this->text()->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),

            'PRIMARY KEY ([[aaguid]])',
        ]);

        $this->createTable('{{%passkey_aaguid_icon}}', [
            'aaguid' =>
                'UUID NOT NULL REFERENCES {{%passkey_aaguid}}([[aaguid]]) ON DELETE CASCADE',
            'theme' => $this->string(8)->notNull()
                ->append("CHECK ([[theme]] IN ('light', 'dark'))"),
            'mime_type' => $this->string(32)->notNull(),
            'data' => $this->binary()->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),

            'PRIMARY KEY ([[aaguid]], [[theme]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropTable('{{%passkey_aaguid_icon}}');
        $this->dropTable('{{%passkey_aaguid}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%passkey_aaguid}}',
            '{{%passkey_aaguid_icon}}',
        ];
    }
}

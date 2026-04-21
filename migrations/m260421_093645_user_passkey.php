<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m260421_093645_user_passkey extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%login_method}}', [
            'id' => 4,
            'name' => 'Passkey',
        ]);

        $this->createTable('{{%user_passkey_user}}', [
            'user_id' =>
                'INTEGER NOT NULL PRIMARY KEY REFERENCES {{user}}([[id]]) ON DELETE CASCADE',
            'user_handle' => $this->string(128)->notNull()->unique(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
        ]);

        $this->createTable('{{%user_passkey}}', [
            'id' => $this->primaryKey(),
            'user_id' =>
                'INTEGER NOT NULL REFERENCES {{%user_passkey_user}}([[user_id]]) ON DELETE CASCADE',
            'credential_id' => $this->string(1400)->notNull()->unique(),
            'public_key' => $this->text()->notNull(),
            'sign_count' => $this->bigInteger()->notNull()->defaultValue(0)
                ->append('CHECK ([[sign_count]] BETWEEN 0 AND 4294967295)'),
            'aaguid' => 'UUID NOT NULL',
            'attestation_format' => $this->string(32)->notNull(),
            'transports' => "TEXT[] NOT NULL DEFAULT '{}'",
            'backup_eligible' => $this->boolean()->notNull()->defaultValue(false),
            'backup_state' => $this->boolean()->notNull()->defaultValue(false),
            'nickname' => $this->string(64)->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
            'last_used_at' => $this->timestampTZ(0)->null(),

            'CHECK ([[backup_eligible]] OR NOT [[backup_state]])',
        ]);

        $this->createIndex(
            'user_passkey_user_id',
            '{{%user_passkey}}',
            ['user_id'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_passkey}}');
        $this->dropTable('{{%user_passkey_user}}');
        $this->delete('{{%login_method}}', ['id' => 4]);

        return true;
    }
}

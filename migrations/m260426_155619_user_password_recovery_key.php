<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use yii\db\Connection;

final class m260426_155619_user_password_recovery_key extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $db = $this->db;
        assert($db instanceof Connection);

        $this->createTable('{{%user_password_recovery_key_revoke_reason}}', [
            'id' => $this->integer()->notNull(),
            'key' => $this->string(32)->notNull()->unique(),
            'name' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[id]])',
        ]);

        $this->batchInsert(
            '{{%user_password_recovery_key_revoke_reason}}',
            ['id', 'key', 'name'],
            [
                [1, 'user_request', 'Revoked by the user'],
                [2, 'password_changed', 'Revoked because the password was changed'],
                [3, 'set_regenerated', 'Revoked because the recovery key set was regenerated'],
            ],
        );

        $this->createTable('{{%user_password_recovery_key}}', [
            'id' => $this->primaryKey(),
            'user_id' =>
                'INTEGER NOT NULL REFERENCES {{user}}([[id]]) ON DELETE CASCADE',
            'public_id' => 'UUID NOT NULL UNIQUE',
            'secret_hash' => $this->string(255)->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'created_ip' => 'INET NULL',
            'used_at' => $this->timestampTZ(0)->null(),
            'used_ip' => 'INET NULL',
            'revoked_at' => $this->timestampTZ(0)->null(),
            'revoked_reason' =>
                'INTEGER NULL REFERENCES ' .
                '{{%user_password_recovery_key_revoke_reason}}([[id]])',

            'CHECK (([[used_at]] IS NULL) = ([[used_ip]] IS NULL))',
            'CHECK (([[revoked_at]] IS NULL) = ([[revoked_reason]] IS NULL))',
        ]);

        $this->createIndex(
            'user_password_recovery_key_user_id',
            '{{%user_password_recovery_key}}',
            ['user_id'],
        );

        $this->execute(
            vsprintf('CREATE INDEX %s ON %s (%s) WHERE ((%s))', [
                $db->quoteColumnName('user_password_recovery_key_active'),
                $db->quoteTableName('{{%user_password_recovery_key}}'),
                $db->quoteColumnName('user_id'),
                implode(') AND (', [
                    '[[used_at]] IS NULL',
                    '[[revoked_at]] IS NULL',
                ]),
            ]),
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropTable('{{%user_password_recovery_key}}');
        $this->dropTable('{{%user_password_recovery_key_revoke_reason}}');

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    protected function vacuumTables(): array
    {
        return [
            '{{%user_password_recovery_key}}',
            '{{%user_password_recovery_key_revoke_reason}}',
        ];
    }
}

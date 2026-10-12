<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

/**
 * Stores pending WebAuthn challenges outside the session.
 *
 * The session (yii\web\DbSession) is read and written without locking, so a
 * challenge kept there can be used by two concurrent requests. Deleting the
 * row with DELETE ... RETURNING takes a challenge atomically.
 */
final class m261012_142841_passkey_challenge extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        $this->createTable('{{%passkey_challenge}}', [
            'token' => $this->text()->notNull()->append('PRIMARY KEY'),
            'purpose' => $this->text()->notNull()
                ->append("CHECK ([[purpose]] IN ('login', 'reauth', 'register', 'signup'))"),
            'user_id' => $this->integer()->null()
                ->append('REFERENCES {{%user}}([[id]]) ON DELETE CASCADE'),
            'challenge' => $this->text()->notNull(),
            'payload' => 'JSONB NULL',
            'expires_at' => $this->timestampTZ(0)->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
        ]);

        $this->createIndex(
            'passkey_challenge_expires_at',
            '{{%passkey_challenge}}',
            ['expires_at'],
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropTable('{{%passkey_challenge}}');

        return true;
    }
}

<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m260523_052910_login_with_google extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->insert('{{%login_method}}', [
            'id' => 5,
            'name' => 'Google',
        ]);

        $this->createTable('{{%login_with_google}}', [
            'user_id' =>
                'INTEGER NOT NULL PRIMARY KEY REFERENCES {{%user}}([[id]]) ON DELETE CASCADE',
            'google_id' => $this->string(64)->notNull()->unique(),
            'email' => $this->text()->null(),
            'name' => $this->text()->notNull(),
            'created_at' => $this->timestampTZ(0)->notNull(),
            'updated_at' => $this->timestampTZ(0)->notNull(),
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%login_with_google}}');
        $this->delete('{{%login_method}}', ['id' => 5]);

        return true;
    }
}

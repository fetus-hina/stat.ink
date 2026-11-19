<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m261119_053831_user_badge3_series_win extends Migration
{
    /**
     * @inheritdoc
     */
    #[Override]
    public function safeUp()
    {
        // The initial data is populated by the next migration (m261119_090802_user_badge3_series_win_data)
        // so that the lock on the referenced tables (user, lobby3) is released quickly.
        $this->createTable('{{%user_badge3_series_win}}', [
            'user_id' => (string)$this->pkRef('{{%user}}')->notNull(),
            'lobby_id' => (string)$this->pkRef('{{%lobby3}}')->notNull(),
            'count' => (string)$this->bigInteger()->notNull()->append('CHECK ([[count]] > 0)'),
            'created_at' => (string)$this->timestampTZ(0)->notNull(),
            'updated_at' => (string)$this->timestampTZ(0)->notNull(),
            'PRIMARY KEY ([[user_id]], [[lobby_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    #[Override]
    public function safeDown()
    {
        $this->dropTable('{{%user_badge3_series_win}}');

        return true;
    }
}

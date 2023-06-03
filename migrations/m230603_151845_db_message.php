<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230603_151845_db_message extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%translate_source_message}}', [
            'id' => $this->primaryKey(),
            'category' => $this->string()->notNull(),
            'message' => $this->text()->notNull(),
        ]);

        $this->createTable('{{%translate_message}}', [
            'id' => $this->pkRef('{{%translate_source_message}}')->notNull(),
            'language' => $this->string(16)->notNull(),
            'translation' => $this->text()->notNull(),
            'PRIMARY KEY ([[id]], [[language]])',
        ]);

        $this->createIndex(
            'idx_translate_source_message_category',
            '{{%translate_source_message}}',
            ['category', 'message'],
        );

        $this->createIndex(
            'idx_translate_message_language',
            '{{%translate_message}}',
            ['language', 'id'],
            true,
        );

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTables([
            '{{%translate_message}}',
            '{{%translate_source_message}}',
        ]);

        return true;
    }
}

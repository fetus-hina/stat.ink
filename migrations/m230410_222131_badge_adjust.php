<?php

/**
 * @copyright Copyright (C) 2015-2023 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m230410_222131_badge_adjust extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%user_badge3_adjust}}', [
            'user_id' => $this->pkRef('user')->notNull(),
            'data' => 'JSONB NOT NULL',

            'PRIMARY KEY ([[user_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%user_badge3_adjust}}');

        return true;
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

final class m220920_132046_battle3_ua_variable extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->createTable('{{%agent_variable3}}', [
            'id' => $this->primaryKey(),
            'key' => $this->string(63)->notNull(),
            'value' => $this->string(255)->notNull(),
            'UNIQUE ([[key]], [[value]])',
        ]);

        $this->createTable('{{%battle_agent_variable3}}', [
            'battle_id' => $this->bigPkRef('{{%battle3}}')->notNull(),
            'variable_id' => $this->pkRef('{{%agent_variable3}}')->notNull(),
            'PRIMARY KEY ([[battle_id]], [[variable_id]])',
        ]);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropTable('{{%battle_agent_variable3}}');
        $this->dropTable('{{%agent_variable3}}');

        return true;
    }
}

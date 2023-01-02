<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160212_134521_agent_stats extends Migration
{
    public function up()
    {
        $this->createTable('stat_agent_user', [
            'id' => $this->primaryKey(),
            'agent' => 'VARCHAR(64) NOT NULL',
            'date' => 'DATE NOT NULL',
            'battle_count' => 'BIGINT NOT NULL',
            'user_count' => 'BIGINT NOT NULL',
        ]);
        $this->createIndex('ix_stat_agent_user_1', 'stat_agent_user', ['agent', 'date'], true);
    }

    public function down()
    {
        $this->dropTable('stat_agent_user');
    }
}

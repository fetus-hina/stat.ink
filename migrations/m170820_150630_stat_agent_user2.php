<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170820_150630_stat_agent_user2 extends Migration
{
    public function up()
    {
        $this->createTable('stat_agent_user2', [
            'id' => $this->primaryKey(),
            'agent' => 'VARCHAR(64) NOT NULL',
            'date' => 'DATE NOT NULL',
            'battle_count' => 'BIGINT NOT NULL',
            'user_count' => 'BIGINT NOT NULL',
            'UNIQUE ([[agent]], [[date]])',
        ]);
    }

    public function down()
    {
        $this->dropTable('stat_agent_user2');
    }
}

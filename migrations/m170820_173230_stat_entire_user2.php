<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170820_173230_stat_entire_user2 extends Migration
{
    public function up()
    {
        $this->createTable('stat_entire_user2', [
            'date' => 'DATE NOT NULL PRIMARY KEY',
            'battle_count' => 'BIGINT NOT NULL',
            'user_count' => 'BIGINT NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('stat_entire_user2');
    }
}

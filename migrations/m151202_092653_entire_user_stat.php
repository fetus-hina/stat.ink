<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151202_092653_entire_user_stat extends Migration
{
    public function up()
    {
        $this->createTable('stat_entire_user', [
            'date' => 'DATE NOT NULL PRIMARY KEY',
            'battle_count' => 'BIGINT NOT NULL',
            'user_count' => 'BIGINT NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('stat_entire_user');
    }
}

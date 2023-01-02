<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171015_165205_timezone_group extends Migration
{
    public function up()
    {
        $this->createTable('timezone_group', [
            'id' => $this->primaryKey(),
            'order' => $this->integer()->notNull()->unique(),
            'name' => $this->string()->notNull(),
        ]);
        $this->batchInsert('timezone_group', ['order', 'name'], [
            [ 10, 'East Asia' ],
            [ 20, 'Australia/Oceania' ],
            [ 30, 'Russia' ],
            [ 40, 'Europe' ],
            [ 50, 'North America' ],
            [ 60, 'Latin America' ],
            [ 999, 'Others' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('timezone_group');
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170329_085347_lobby2 extends Migration
{
    public function up()
    {
        $this->createTable('lobby2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->insert('lobby2', [
            'key' => 'standard',
            'name' => 'Solo Queue',
        ]);
    }

    public function down()
    {
        $this->dropTable('lobby2');
    }
}

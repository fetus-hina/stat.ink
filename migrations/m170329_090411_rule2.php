<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170329_090411_rule2 extends Migration
{
    public function up()
    {
        $this->createTable('rule2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'short_name' => $this->string(16)->notNull()->unique(),
        ]);
        $this->insert('rule2', [
            'key' => 'nawabari',
            'name' => 'Turf War',
            'short_name' => 'TW',
        ]);
    }

    public function down()
    {
        $this->dropTable('rule2');
    }
}

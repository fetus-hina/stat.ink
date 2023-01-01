<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170329_084919_splatoon_version2 extends Migration
{
    public function up()
    {
        $this->createTable('splatoon_version2', [
            'id' => $this->primaryKey(),
            'tag' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
            'released_at' => $this->timestampTZ()->notNull(),
        ]);
        $this->insert('splatoon_version2', [
            'tag' => '0.0.1',
            'name' => 'Testfire',
            'released_at' => '2017-03-25 04:00:00+09',
        ]);
    }

    public function down()
    {
        $this->dropTable('splatoon_version2');
    }
}

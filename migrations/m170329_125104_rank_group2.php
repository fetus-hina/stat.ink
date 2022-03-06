<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170329_125104_rank_group2 extends Migration
{
    public function up()
    {
        $this->createTable('rank_group2', [
            'id'    => $this->primaryKey(),
            'rank'  => $this->integer()->notNull()->unique(),
            'key'   => $this->apiKey(),
            'name'  => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('rank_group2', ['rank', 'key', 'name'], [
            [ 10, 'c', 'C zone' ],
            [ 20, 'b', 'B zone' ],
            [ 30, 'a', 'A zone' ],
            [ 40, 's', 'S zone' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('rank_group2');
    }
}

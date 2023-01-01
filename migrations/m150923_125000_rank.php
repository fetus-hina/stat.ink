<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150923_125000_rank extends Migration
{
    public function up()
    {
        $this->createTable('rank', [
            'id' => $this->primaryKey(),
            'key' => $this->string(16)->notNull()->unique(),
            'name' => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('rank', ['key', 'name'], [
            [ 'c-', 'C-' ],
            [ 'c', 'C' ],
            [ 'c+', 'C+' ],
            [ 'b-', 'B-' ],
            [ 'b', 'B' ],
            [ 'b+', 'B+' ],
            [ 'a-', 'A-' ],
            [ 'a', 'A' ],
            [ 'a+', 'A+' ],
            [ 's', 'S' ],
            [ 's+', 'S+' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('rank');
    }
}

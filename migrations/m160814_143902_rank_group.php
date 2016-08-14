<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160814_143902_rank_group extends Migration
{
    public function up()
    {
        $this->createTable('rank_group', [
            'id' => $this->primarykey(),
            'key' => $this->string(16)->notNull()->unique(),
            'name' => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('rank_group', ['key', 'name'], [
            [ 'c', 'C zone' ],
            [ 'b', 'B zone' ],
            [ 'a', 'A zone' ],
            [ 's', 'S zone' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('rank_group');
    }
}

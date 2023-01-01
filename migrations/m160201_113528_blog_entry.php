<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160201_113528_blog_entry extends Migration
{
    public function up()
    {
        $this->createTable('blog_entry', [
            'id' => $this->primaryKey(),
            'uuid' => sprintf('CHAR(%d) NOT NULL UNIQUE', strlen('6ba7b811-9dad-11d1-80b4-00c04fd430c8')),
            'url' => 'VARCHAR(256) NOT NULL',
            'title' => 'VARCHAR(256) NOT NULL',
            'at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('blog_entry');
    }
}

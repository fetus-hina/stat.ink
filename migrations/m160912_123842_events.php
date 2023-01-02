<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160912_123842_events extends Migration
{
    public function up()
    {
        $this->createTable('event', [
            'id' => $this->primaryKey(),
            'date' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
            'name' => $this->string(64)->notNull(),
            'icon' => $this->string(32)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('event');
    }
}

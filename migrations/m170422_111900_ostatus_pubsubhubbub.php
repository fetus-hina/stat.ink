<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170422_111900_ostatus_pubsubhubbub extends Migration
{
    public function up()
    {
        $this->createTable('ostatus_pubsubhubbub', [
            'id' => $this->primaryKey(),
            'topic' => $this->pkRef('user'),
            'callback' => $this->string(255)->notNull(),
            'lease_until' => $this->timestampTZ(),
            'secret' => $this->string(200),
            'created_at' => $this->timestampTZ()->notNull(),
            'updated_at' => $this->timestampTZ()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('ostatus_pubsubhubbub');
    }
}

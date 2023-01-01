<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171103_171616_gear2 extends Migration
{
    public function up()
    {
        $this->createTable('gear2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(32),
            'type_id' => $this->pkRef('gear_type'),
            'brand_id' => $this->pkRef('brand2'),
            'name' => $this->string(32)->notNull(),
            'ability_id' => $this->pkRef('ability2')->null(),
            'splatnet' => $this->integer()->unique()->null(),
        ]);
    }

    public function down()
    {
        $this->dropTable('gear2');
    }
}

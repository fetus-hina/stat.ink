<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170507_103030_death_reason2 extends Migration
{
    public function up()
    {
        $this->createTable('death_reason_type2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
        ]);
        $this->createTable('death_reason2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'type_id' => $this->pkRef('death_reason_type2')->null(),
            'weapon_id' => $this->pkRef('weapon2')->null(),
            'subweapon_id' => $this->pkRef('subweapon2')->null(),
            'special_id' => $this->pkRef('special2')->null(),
            'name' => $this->string(32)->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('death_reason2');
        $this->dropTable('death_reason_type2');
    }
}

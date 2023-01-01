<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151219_094823_splatfest_map extends Migration
{
    public function up()
    {
        $this->createTable('splatfest_map', [
            'id' => $this->primaryKey(),
            'splatfest_id' => $this->integer()->notNull(),
            'map_id' => $this->integer()->notNull(),
        ]);
        $this->addForeignKey('fk_splatfest_map_1', 'splatfest_map', 'splatfest_id', 'splatfest', 'id');
        $this->addForeignKey('fk_splatfest_map_2', 'splatfest_map', 'map_id', 'map', 'id');
    }

    public function down()
    {
        $this->dropTable('splatfest_map');
    }
}

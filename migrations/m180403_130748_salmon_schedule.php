<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180403_130748_salmon_schedule extends Migration
{
    public function up()
    {
        $this->createTable('salmon_schedule2', [
            'id' => $this->primaryKey(),
            'map_id' => $this->pkRef('salmon_map2'),
            'start_at' => $this->timestampTZ()->notNull(),
            'end_at' => $this->timestampTZ()->notNull(),
        ]);
        $this->createIndex('ix_salmon_schedule2_end_at', 'salmon_schedule2', 'end_at');
        $this->createTable('salmon_weapon2', [
            'id' => $this->primaryKey(),
            'schedule_id' => $this->pkRef('salmon_schedule2'),
            'weapon_id' => $this->pkRef('weapon2')->null(),
        ]);
        $this->createIndex('ix_salmon_weapon2_schedule_id', 'salmon_weapon2', 'schedule_id');
        $this->createIndex('ix_weapon2_splatnet', 'weapon2', 'splatnet', true);
    }

    public function down()
    {
        $this->dropIndex('ix_weapon2_splatnet', 'weapon2');
        $this->dropTable('salmon_weapon2');
        $this->dropTable('salmon_schedule2');
    }
}

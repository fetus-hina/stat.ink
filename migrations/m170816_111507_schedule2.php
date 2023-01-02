<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170816_111507_schedule2 extends Migration
{
    public function up()
    {
        $this->createTable('schedule_mode2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
        ]);
        $this->batchInsert('schedule_mode2', ['key', 'name'], [
            ['regular', 'Regular Battle'],
            ['gachi', 'Ranked Battle'],
            ['league', 'League Battle'],
        ]);
        $this->createTable('schedule2', [
            'id' => $this->primaryKey(),
            'period' => $this->integer()->notNull(),
            'mode_id' => $this->pkRef('schedule_mode2'),
            'rule_id' => $this->pkRef('rule2'),
            'UNIQUE ([[period]], [[mode_id]])',
        ]);
        $this->createTable('schedule_map2', [
            'schedule_id' => $this->pkRef('schedule2'),
            'map_id' => $this->pkRef('map2'),
            'PRIMARY KEY ([[schedule_id]], [[map_id]])',
        ]);
    }

    public function down()
    {
        $this->dropTable('schedule_map2');
        $this->dropTable('schedule2');
        $this->dropTable('schedule_mode2');
    }
}

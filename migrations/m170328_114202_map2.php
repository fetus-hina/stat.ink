<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170328_114202_map2 extends Migration
{
    public function up()
    {
        $this->createTable('map2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
            'short_name' => $this->string(16)->notNull()->unique(),
            'area' => $this->integer()->null(),
            'release_at' => $this->timestampTZ()->null(),
        ]);
        $this->batchInsert('map2', ['key', 'name', 'short_name', 'area', 'release_at'], [
            [
                'battera',
                'The Reef',
                'Reef',
                2450,
                '2017-03-25 04:00:00+09',
            ],
            [
                'fujitsubo',
                'Musselforge Fitness',
                'Fitness',
                1957,
                '2017-03-25 04:00:00+09',
            ],
            [
                'gangaze',
                'Diadema Amphitheater',
                'Amphitheater',
                null,
                null,
            ],
        ]);
    }

    public function down()
    {
        $this->dropTable('map2');
    }
}

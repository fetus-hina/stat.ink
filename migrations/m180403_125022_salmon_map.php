<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180403_125022_salmon_map extends Migration
{
    public function up()
    {
        $this->createTable('salmon_map2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull(),
        ]);
        $this->batchInsert('salmon_map2', ['key', 'name'], [
            [
                'damu',
                'Spawning Grounds',
            ],
            [
                'donburako',
                'Marooner\'s Bay',
            ],
            [
                'shaketoba',
                'Lost Outpost',
            ],
            [
                'tokishirazu',
                'Salmonid Smokeyard',
            ],
        ]);
    }

    public function down()
    {
        $this->dropTable('salmon_map2');
    }
}

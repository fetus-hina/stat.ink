<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151219_090211_region extends Migration
{
    public function up()
    {
        $this->createTable('region', [
            'id' => $this->primaryKey(),
            'key' => 'CHAR(2) NOT NULL UNIQUE',
            'name' => 'VARCHAR(64) NOT NULL',
        ]);
        $this->batchInsert('region', ['key', 'name'], [
            [ 'jp', 'Japan' ],
            [ 'eu', 'Europe/Oceania' ],
            [ 'na', 'North America' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('region');
    }
}

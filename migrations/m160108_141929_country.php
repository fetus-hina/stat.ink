<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160108_141929_country extends Migration
{
    public function up()
    {
        $this->createTable('country', [
            'id' => $this->primaryKey(),
            'key' => 'CHAR(2) NOT NULL UNIQUE',
            'name' => 'VARCHAR(32) NOT NULL',
        ]);
        $this->batchInsert('country', ['key', 'name'], [
            [ 'au', 'Australia' ],
            [ 'ca', 'Canada' ],
            [ 'eu', 'Europe' ], // OK, I know, It's not a country.
            [ 'jp', 'Japan' ],
            [ 'us', 'United States' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('country');
    }
}

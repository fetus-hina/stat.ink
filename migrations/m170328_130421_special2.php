<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170328_130421_special2 extends Migration
{
    public function up()
    {
        $this->createTable('special2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('special2', ['key', 'name'], [
            [ 'chakuchi', 'Splashdown' ],
            [ 'jetpack', 'Inkjet' ],
            [ 'missile', 'Tenta Missiles' ],
            [ 'presser', 'Sting Ray' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('special2');
    }
}

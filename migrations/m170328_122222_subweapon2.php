<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170328_122222_subweapon2 extends Migration
{
    public function up()
    {
        $this->createTable('subweapon2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('subweapon2', ['key', 'name'], [
            [ 'curlingbomb', 'Curling Bomb' ],
            [ 'kyubanbomb', 'Suction Bomb' ],
            [ 'quickbomb', 'Burst Bomb' ],
            [ 'splashbomb', 'Splat Bomb' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('subweapon2');
    }
}

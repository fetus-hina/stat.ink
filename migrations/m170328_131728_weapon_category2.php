<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170328_131728_weapon_category2 extends Migration
{
    public function up()
    {
        $this->createTable('weapon_category2', [
            'id' => $this->primaryKey(),
            'key' => $this->apiKey(),
            'name' => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('weapon_category2', ['key', 'name'], [
            [ 'shooter', 'Shooters' ],
            [ 'roller', 'Rollers' ],
            [ 'charger', 'Chargers' ],
            [ 'slosher', 'Sloshers' ],
            [ 'splatling', 'Splatlings' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon_category2');
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150923_123420_weapon_type extends Migration
{
    public function up()
    {
        $this->createTable('weapon_type', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(16)->notNull()->unique(),
            'name'  => $this->string(16)->notNull()->unique(),
        ]);
        $this->batchInsert('weapon_type', ['key', 'name'], [
            [ 'shooter',   'Shooters' ],
            [ 'roller',    'Rollers' ],
            [ 'charger',   'Chargers' ],
            [ 'slosher',   'Sloshers' ],
            [ 'splatling', 'Splatlings' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('weapon_type');
    }
}

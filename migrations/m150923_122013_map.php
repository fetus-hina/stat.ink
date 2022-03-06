<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150923_122013_map extends Migration
{
    public function up()
    {
        $this->createTable('map', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(16)->notNull()->unique(),
            'name'  => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('map', ['key', 'name'], [
            [ 'arowana',    'Arowana Mall' ],
            [ 'bbass',      'Blackbelly Skatepark' ],
            [ 'shionome',   'Saltspray Rig' ],
            [ 'dekaline',   'Urchin Underpass' ],
            [ 'hakofugu',   'Walleye Warehouse' ],
            [ 'hokke',      'Port Mackerel' ],
            [ 'mozuku',     'Kelp Dome' ],
            [ 'negitoro',   'Bluefin Depot' ],
            [ 'tachiuo',    'Moray Towers' ],
            [ 'mongara',    'Camp Triggerfish' ],
            [ 'hirame',     'Flounder Heights' ],
            [ 'masaba',     'Hammerhead Bridge' ],
        ]);
    }

    public function down()
    {
        $this->dropTable('map');
    }
}

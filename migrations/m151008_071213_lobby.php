<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151008_071213_lobby extends Migration
{
    public function up()
    {
        $this->createTable('lobby', [
            'id'    => $this->primaryKey(),
            'key'   => $this->string(16)->notNull()->unique(),
            'name'  => $this->string(32)->notNull()->unique(),
        ]);
        $this->batchInsert('lobby', ['key', 'name'], [
            [ 'standard', 'Standard Battle' ],
            [ 'squad_2', 'Squad Battle (2 Players)' ],
            [ 'squad_3', 'Squad Battle (3 Players)' ],
            [ 'squad_4', 'Squad Battle (4 Players)' ],
            [ 'private', 'Private Battle' ],
            [ 'fest', 'Splatfest' ],
        ]);

        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[lobby_id]] INTEGER');
        $this->addForeignKey('fk_battle_8', 'battle', 'lobby_id', 'lobby', 'id');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[lobby_id]]');
        $this->dropTable('lobby');
    }
}

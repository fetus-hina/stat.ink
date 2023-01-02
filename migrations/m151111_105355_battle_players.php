<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151111_105355_battle_players extends Migration
{
    public function up()
    {
        $this->createTable('battle_player', [
            'id' => $this->bigPrimaryKey(),
            'battle_id' => $this->bigInteger()->notNull(),
            'is_my_team' => $this->boolean()->notNull(),
            'is_me' => $this->boolean()->notNull(),
            'weapon_id' => $this->integer(),
            'rank_id' => $this->integer(),
            'level' => $this->integer(),
            'rank_in_team' => $this->integer(),
            'kill' => $this->integer(),
            'death' => $this->integer(),
            'point' => $this->integer(),
        ]);
        $this->addForeignKey('fk_battle_players_1', 'battle_player', 'battle_id', 'battle', 'id', 'CASCADE');
        $this->addForeignKey('fk_battle_players_2', 'battle_player', 'weapon_id', 'weapon', 'id');
        $this->addForeignKey('fk_battle_players_3', 'battle_player', 'rank_id', 'rank', 'id');
    }

    public function down()
    {
        $this->dropTable('battle_player');
    }
}

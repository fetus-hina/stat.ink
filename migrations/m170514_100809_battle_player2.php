<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170514_100809_battle_player2 extends Migration
{
    public function up()
    {
        $this->createTable('battle_player2', [
            'id' => $this->bigPrimaryKey(),
            'battle_id' => $this->bigPkRef('battle2', 'id'),
            'is_my_team' => $this->boolean()->notNull(),
            'is_me' => $this->boolean()->notNull(),
            'weapon_id' => $this->pkRef('weapon2', 'id')->null(),
            'level' => $this->integer()->null(),
            'rank_in_team' => $this->integer()->null(),
            'kill' => $this->integer()->null(),
            'death' => $this->integer()->null(),
            'point' => $this->integer()->null(),
            'my_kill' => $this->integer()->null(),
        ]);
    }

    public function down()
    {
        $this->dropTable('battle_player2');
    }
}

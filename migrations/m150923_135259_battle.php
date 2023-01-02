<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m150923_135259_battle extends Migration
{
    public function up()
    {
        $this->createTable('battle', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->integer()->notNull(),
            'rule_id' => $this->integer(),
            'map_id' => $this->integer(),
            'weapon_id' => $this->integer(),
            'level' => $this->integer(),
            'rank_id' => $this->integer(),
            'is_win' => $this->boolean(),
            'rank_in_team' => $this->integer(),
            'kill' => $this->integer(),
            'death' => $this->integer(),
            'start_at' => 'TIMESTAMP(0) WITH TIME ZONE',
            'end_at' => 'TIMESTAMP(0) WITH TIME ZONE',
            'agent' => $this->string(16),
            'agent_version' => $this->string(16),
            'at' => 'TIMESTAMP(0) WITH TIME ZONE NOT NULL',
        ]);
        $this->addForeignKey('fk_battle_1', 'battle', 'user_id', 'user', 'id', 'RESTRICT');
        $this->addForeignKey('fk_battle_2', 'battle', 'rule_id', 'rule', 'id', 'RESTRICT');
        $this->addForeignKey('fk_battle_3', 'battle', 'map_id', 'map', 'id', 'RESTRICT');
        $this->addForeignKey('fk_battle_4', 'battle', 'weapon_id', 'weapon', 'id', 'RESTRICT');
        $this->addForeignKey('fk_battle_5', 'battle', 'rank_id', 'rank', 'id', 'RESTRICT');

        $this->createTable('battle_nawabari', [
            'id' => $this->bigInteger()->notNull(),
            'my_point' => $this->integer(),
            'my_team_final_point' => $this->integer(),
            'his_team_final_point' => $this->integer(),
            'my_team_final_percent' => $this->decimal(4, 1),
            'his_team_final_percent' => $this->decimal(4, 1),
        ]);
        $this->addPrimaryKey('pk_battle_nawabari', 'battle_nawabari', 'id');
        $this->addForeignKey('fk_battle_nawabari_1', 'battle_nawabari', 'id', 'battle', 'id', 'RESTRICT');

        $this->createTable('battle_gachi', [
            'id' => $this->bigInteger()->notNull(),
            'is_knock_out' => $this->boolean(),
            'my_team_count' => $this->integer(),
            'his_team_count' => $this->integer(),
        ]);
        $this->addPrimaryKey('pk_battle_gachi', 'battle_gachi', 'id');
        $this->addForeignKey('fk_battle_gachi_1', 'battle_gachi', 'id', 'battle', 'id', 'RESTRICT');
    }

    public function down()
    {
        $this->dropTable('battle_gachi');
        $this->dropTable('battle_nawabari');
        $this->dropTable('battle');
    }
}

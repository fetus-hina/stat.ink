<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m170901_124649_splatnet_ids extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[splatnet_number]] BIGINT NULL',
            'ADD COLUMN [[my_team_id]] VARCHAR(16) NULL',
            'ADD COLUMN [[his_team_id]] VARCHAR(16) NULL',
        ]));
        $this->createIndex('ix_battle2_my_team_id', 'battle2', 'my_team_id');
        $this->createIndex('ix_battle2_his_team_id', 'battle2', 'his_team_id');
        $this->createIndex('ix_battle2_splatnet_number', 'battle2', ['user_id', 'splatnet_number']);
        $this->createTable('battle2_splatnet', [
            'id' => $this->bigPkRef('battle2'),
            'json' => 'JSONB NOT NULL',
            'PRIMARY KEY ([[id]])',
        ]);
        $this->addColumn('battle_player2', 'splatnet_id', 'VARCHAR(16)');
        $this->createIndex('ix_battle_player2_splatnet_id', 'battle_player2', 'splatnet_id');
    }

    public function down()
    {
        $this->dropColumn('battle_player2', 'splatnet_id');
        $this->dropTable('battle2_splatnet');
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[splatnet_number]]',
            'DROP COLUMN [[my_team_id]]',
            'DROP COLUMN [[his_team_id]]',
        ]));
    }
}

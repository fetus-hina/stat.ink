<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170906_154159_playerid_map extends Migration
{
    public function up()
    {
        $this->createTable('splatnet2_user_map', [
            'splatnet_id' => $this->string(16)->notNull(),
            'user_id' => $this->pkRef('user'),
            'battles' => $this->bigInteger()->notNull(),
            'PRIMARY KEY ([[splatnet_id]], [[user_id]])',
        ]);
        $this->createIndex('ix_splatnet2_user_map_1', 'splatnet2_user_map', 'user_id');
        $this->execute(
            'INSERT INTO {{splatnet2_user_map}} ([[splatnet_id]], [[user_id]], [[battles]]) ' .
            'SELECT {{battle_player2}}.[[splatnet_id]], {{battle2}}.[[user_id]], COUNT(*) ' .
            'FROM {{battle_player2}} ' .
            'INNER JOIN {{battle2}} ON {{battle_player2}}.[[battle_id]] = {{battle2}}.[[id]] ' .
            'WHERE {{battle_player2}}.[[splatnet_id]] IS NOT NULL ' .
            'AND {{battle_player2}}.[[is_me]] = TRUE ' .
            'GROUP BY {{battle_player2}}.[[splatnet_id]], {{battle2}}.[[user_id]] ' .
            'ON CONFLICT ([[splatnet_id]], [[user_id]]) DO NOTHING ',
        );
        $this->execute('VACUUM ANALYZE {{splatnet2_user_map}}');
    }

    public function down()
    {
        $this->dropTable('splatnet2_user_map');
    }
}

<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160512_110239_battle_agent_game_version extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[agent_game_version_id]] INTEGER',
            'ADD COLUMN [[agent_game_version_date]] VARCHAR(64)',
        ]));
        $this->addForeignKey('fk_battle_16', 'battle', 'agent_game_version_id', 'splatoon_version', 'id');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'DROP COLUMN [[agent_game_version_id]]',
            'DROP COLUMN [[agent_game_version_date]]',
        ]));
    }
}

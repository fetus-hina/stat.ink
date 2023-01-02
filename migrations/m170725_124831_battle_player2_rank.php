<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170725_124831_battle_player2_rank extends Migration
{
    public function up()
    {
        $this->execute(
            'ALTER TABLE {{battle_player2}} ' .
            'ADD COLUMN [[rank_id]] INTEGER REFERENCES {{rank2}}([[id]])',
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle_player2}} DROP COLUMN [[rank_id]]');
    }
}

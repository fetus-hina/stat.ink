<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m170719_180753_results_screen2 extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[kill_or_assist]] INTEGER',
            'ADD COLUMN [[special]] INTEGER',
        ]));
        $this->execute('ALTER TABLE {{battle_player2}} ' . implode(', ', [
            'ADD COLUMN [[kill_or_assist]] INTEGER',
            'ADD COLUMN [[special]] INTEGER',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[kill_or_assist]]',
            'DROP COLUMN [[special]]',
        ]));
        $this->execute('ALTER TABLE {{battle_player2}} ' . implode(', ', [
            'DROP COLUMN [[kill_or_assist]]',
            'DROP COLUMN [[special]]',
        ]));
    }
}

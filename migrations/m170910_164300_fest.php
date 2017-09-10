<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m170910_164300_fest extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[fest_power]] DECIMAL(6, 1) NULL',
            'ADD COLUMN [[my_team_estimate_fest_power]] INTEGER NULL',
            'ADD COLUMN [[his_team_estimate_fest_power]] INTEGER NULL',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[fest_power]]',
            'DROP COLUMN [[my_team_estimate_fest_power]]',
            'DROP COLUMN [[his_team_estimate_fest_power]]',
        ]));
    }
}

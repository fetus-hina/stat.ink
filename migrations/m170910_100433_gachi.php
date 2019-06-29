<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170910_100433_gachi extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[estimate_gachi_power]] INTEGER NULL',
            'ADD COLUMN [[league_point]] DECIMAL(6, 1) NULL', // maybe (5, 1)
            'ADD COLUMN [[my_team_estimate_league_point]] INTEGER NULL',
            'ADD COLUMN [[his_team_estimate_league_point]] INTEGER NULL',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[estimate_gachi_power]]',
            'DROP COLUMN [[league_point]]',
            'DROP COLUMN [[my_team_estimate_league_point]]',
            'DROP COLUMN [[his_team_estimate_league_point]]',
        ]));
    }
}

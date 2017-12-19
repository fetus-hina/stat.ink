<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m171219_120310_battle2_splatfest extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[my_team_fest_theme_id]] INTEGER NULL REFERENCES {{splatfest2_theme}}([[id]])',
            'ADD COLUMN [[his_team_fest_theme_id]] INTEGER NULL REFERENCES {{splatfest2_theme}}([[id]])',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[my_team_fest_theme_id]]',
            'DROP COLUMN [[his_team_fest_theme_id]]',
        ]));
    }
}

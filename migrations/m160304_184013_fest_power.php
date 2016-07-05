<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160304_184013_fest_power extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[my_team_power]] INTEGER',
            'ADD COLUMN [[his_team_power]] INTEGER',
            'ADD COLUMN [[fest_power]] INTEGER',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'DROP COLUMN [[my_team_power]]',
            'DROP COLUMN [[his_team_power]]',
            'DROP COLUMN [[fest_power]]',
        ]));
    }
}

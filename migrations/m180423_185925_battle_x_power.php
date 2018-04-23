<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180423_185925_battle_x_power extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'ADD COLUMN [[x_power]] NUMERIC(6, 1) NULL',
            'ADD COLUMN [[x_power_after]] NUMERIC(6, 1) NULL',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle2}} ' . implode(', ', [
            'DROP COLUMN [[x_power]]',
            'DROP COLUMN [[x_power_after]]',
        ]));
    }
}

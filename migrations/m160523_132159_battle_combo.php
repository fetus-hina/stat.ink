<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160523_132159_battle_combo extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'ADD COLUMN [[max_kill_combo]] INTEGER',
            'ADD COLUMN [[max_kill_streak]] INTEGER',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} ' . implode(', ', [
            'DROP COLUMN [[max_kill_combo]]',
            'DROP COLUMN [[max_kill_streak]]',
        ]));
    }
}

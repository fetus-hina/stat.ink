<?php
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

<?php
use yii\db\Migration;

class m151120_215458_fest extends Migration
{
    public function up()
    {
        $this->execute(
            'ALTER TABLE {{battle}} ' . implode(', ', [
                'ADD COLUMN [[fest_title_after_id]] INTEGER',
                'ADD COLUMN [[fest_exp]] INTEGER',
                'ADD COLUMN [[fest_exp_after]] INTEGER',
            ])
        );
        $this->addForeignKey('fk_battle_12', 'battle', 'fest_title_after_id', 'fest_title', 'id');
    }

    public function down()
    {
        $this->execute(
            'ALTER TABLE {{battle}} ' . implode(', ', [
                'DROP COLUMN [[fest_title_after_id]]',
                'DROP COLUMN [[fest_exp]]',
                'DROP COLUMN [[fest_exp_after]]',
            ])
        );
    }
}

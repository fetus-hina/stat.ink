<?php
use yii\db\Migration;

class m160623_132227_my_kill extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle_player}} ADD COLUMN [[my_kill]] INTEGER');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle_player}} DROP COLUMN [[my_kill]]');
    }
}

<?php
use yii\db\Migration;

class m151210_085226_mahimahi_area extends Migration
{
    public function safeUp()
    {
        $this->update('map', ['area' => 1950], ['key' => 'mahimahi']);
    }

    public function safeDown()
    {
        $this->update('map', ['area' => null], ['key' => 'mahimahi']);
    }
}

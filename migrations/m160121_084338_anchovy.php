<?php
use yii\db\Migration;

class m160121_084338_anchovy extends Migration
{
    public function safeUp()
    {
        $this->update('map', ['release_at' => '2016-01-22 11:00:00+09'], ['key' => 'anchovy']);
    }

    public function safeDown()
    {
        $this->update('map', ['release_at' => null], ['key' => 'anchovy']);
    }
}

<?php
use yii\db\Migration;

class m160131_135635_anchovy_area extends Migration
{
    public function safeUp()
    {
        $this->update('map', [
            'area' => 2293,
        ], [
            'key' => 'anchovy'
        ]);
    }

    public function safeDown()
    {
        $this->update('map', [
            'area' => null,
        ], [
            'key' => 'anchovy'
        ]);
    }
}

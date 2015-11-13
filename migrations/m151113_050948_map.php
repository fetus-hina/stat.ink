<?php
use yii\db\Migration;

class m151113_050948_map extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map', [ 'key', 'name' ], [
            ['kinmedai', 'Museum d\'Alfonsino'],
            ['mahimahi', 'Mahi-Mahi Resort'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('map', ['key' => ['kinmedai', 'mahimahi']]);
    }
}

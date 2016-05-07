<?php
use yii\db\Migration;

class m160507_052718_image_gear extends Migration
{
    public function safeUp()
    {
        $this->insert('battle_image_type', [
            'id' => 3,
            'name' => 'ギア',
        ]);
    }

    public function safeDown()
    {
        $this->delete('battle_image_type', ['id' => 3]);
    }
}

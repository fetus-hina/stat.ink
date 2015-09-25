<?php
use yii\db\Migration;
use app\models\WeaponType;

class m150925_083707_weapon_hissen extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'slosher'])->id,
            'key' => 'hissen',
            'name' => 'ヒッセン',
        ]);
    }

    public function safeDown()
    {
        $this->delete('weapon', 'key = :key', [':key' => 'hissen']);
    }
}

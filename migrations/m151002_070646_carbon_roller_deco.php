<?php
use yii\db\Migration;
use app\models\Special;
use app\models\Subweapon;
use app\models\WeaponType;

class m151002_070646_carbon_roller_deco extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'roller'])->id,
            'key' => 'carbon_deco',
            'name' => 'Carbon Roller Deco',
            'subweapon_id' => Subweapon::findOne(['key' => 'chasebomb'])->id,
            'special_id' => Special::findOne(['key' => 'bombrush'])->id,
        ]);
    }

    public function safeDown()
    {
        $this->delete('weapon', 'key = :key', [':key' => 'carbon_deco']);
    }
}

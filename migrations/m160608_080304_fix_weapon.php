<?php
use yii\db\Migration;
use app\models\Special;
use app\models\Subweapon;

class m160608_080304_fix_weapon extends Migration
{
    public function safeUp()
    {
        $this->update(
            'weapon',
            [
                'subweapon_id'  => Subweapon::findOne(['key' => 'sprinkler'])->id,
                'special_id'    => Special::findOne(['key' => 'megaphone'])->id,
            ],
            [
                'key' => 'barrelspinner_remix',
            ]
        );
    }

    public function safeDown()
    {
        $this->update(
            'weapon',
            [
                'subweapon_id'  => Subweapon::findOne(['key' => 'splashbomb'])->id,
                'special_id'    => Special::findOne(['key' => 'supershot'])->id,
            ],
            [
                'key' => 'barrelspinner_remix',
            ]
        );
    }
}

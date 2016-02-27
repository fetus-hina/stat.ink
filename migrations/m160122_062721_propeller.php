<?php
use yii\db\Migration;
use app\models\DeathReasonType;

class m160122_062721_propeller extends Migration
{
    public function safeUp()
    {
        $this->insert('death_reason_type', [
            'key' => 'gadget',
            'name' => 'Gadget',
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'gadget'])->id,
            'key' => 'propeller',
            'name' => 'Ink from a propeller',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'propeller']);
        $this->delete('death_reason_type', ['key' => 'gadget']);
    }
}

<?php
use yii\db\Migration;
use app\models\{
    DeathReason,
    DeathReasonType
};

class m160122_062721_propeller extends Migration
{
    public function safeUp()
    {
        $type = new DeathReasonType();
        $type->attributes = [
            'key' => 'gadget',
            'name' => 'Gadget',
        ];
        if (!$type->save()) {
            return false;
        }

        $reason = new DeathReason();
        $reason->attributes = [
            'type_id'   => $type->id,
            'key'       => 'propeller',
            'name'      => 'Ink from a propeller',
        ];
        if (!$reason->save()) {
            return false;
        }
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'propeller']);
        $this->delete('death_reason_type', ['key' => 'gadget']);
    }
}

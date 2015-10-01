<?php
use yii\db\Migration;

class m151001_100307_fix_timezone extends Migration
{
    public function safeUp()
    {
        $this->update(
            'timezone',
            [ 'name' => 'Hawaii Time' ],
            [ 'identifier' => 'Pacific/Honolulu' ]
        );
    }

    public function safeDown()
    {
        $this->update(
            'timezone',
            [ 'name' => 'Hawaii' ],
            [ 'identifier' => 'Pacific/Honolulu' ]
        );
    }
}

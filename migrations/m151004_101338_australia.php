<?php
use yii\db\Migration;

class m151004_101338_australia extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('timezone', [ 'identifier', 'name', 'order' ], [
            [ 'Australia/Brisbane', 'Australia (East)',         41 ],
            [ 'Australia/Sydney',   'Australia (East, DST)',    42 ],
            [ 'Australia/Adelaide', 'Australia (Central)',      43 ],
            [ 'Australia/Darwin',   'Australia (Central, DST)', 44 ],
            [ 'Australia/Perth',    'Australia (West)',         45 ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('timezone', 'name LIKE :like', [':like' => 'Australia%']);
    }
}

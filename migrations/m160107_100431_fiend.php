<?php
use yii\db\Migration;

class m160107_100431_fiend extends Migration
{
    public function safeUp()
    {
        $this->update(
            'fest_title',
            ['key' => 'fiend'],
            ['key' => 'friend']
        );
        $this->update(
            'fest_title_gender',
            ['name' => '{0} Fiend'],
            ['name' => '{0} Friend']
        );
        $this->update(
            'fest_title_gender',
            ['name' => '{1} Fiend'],
            ['name' => '{1} Friend']
        );
    }

    public function safeDown()
    {
        $this->update(
            'fest_title',
            ['key' => 'friend'],
            ['key' => 'fiend']
        );
        $this->update(
            'fest_title_gender',
            ['name' => '{0} Friend'],
            ['name' => '{0} Fiend']
        );
        $this->update(
            'fest_title_gender',
            ['name' => '{1} Friend'],
            ['name' => '{1} Fiend']
        );
    }
}

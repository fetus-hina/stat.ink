<?php

use yii\db\Schema;
use yii\db\Migration;

class m150906_090807_change_team_codename extends Migration
{
    public function safeUp()
    {
        $this->update(
            'color',
            ['name' => 'alpha'],
            ['id' => 1]
        );
        $this->update(
            'color',
            ['name' => 'bravo'],
            ['id' => 2]
        );
    }

    public function safeDown()
    {
        $this->update(
            'color',
            ['name' => 'red'],
            ['id' => 1]
        );
        $this->update(
            'color',
            ['name' => 'green'],
            ['id' => 2]
        );
    }
}

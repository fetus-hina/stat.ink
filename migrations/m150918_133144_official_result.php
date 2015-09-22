<?php
use yii\db\Migration;

class m150918_133144_official_result extends Migration
{
    public function up()
    {
        $this->createTable('official_result', [
            'fest_id'           => 'INTEGER NOT NULL PRIMARY KEY REFERENCES {{fest}} ( [[id]] )',
            'alpha_people'      => 'INTEGER NOT NULL',
            'bravo_people'      => 'INTEGER NOT NULL',
            'alpha_win'         => 'INTEGER NOT NULL',
            'bravo_win'         => 'INTEGER NOT NULL',
            'win_rate_times'    => 'INTEGER NOT NULL',
        ]);
    }

    public function down()
    {
        $this->dropTable('official_result');
    }
}

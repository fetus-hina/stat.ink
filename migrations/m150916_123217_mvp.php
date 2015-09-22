<?php
use yii\db\Migration;

class m150916_123217_mvp extends Migration
{
    public function up()
    {
        $this->createTable('mvp', [
            'id'        => $this->primaryKey(),
            'data_id'   => 'INTEGER NOT NULL REFERENCES {{official_data}} ( [[id]] )',
            'color_id'  => 'INTEGER NOT NULL REFERENCES {{color}} ( [[id]] )',
            'name'      => $this->string(10)->notNull(),
        ]);
        $this->createIndex('ix_mvp_1', 'mvp', 'data_id', false);
    }

    public function down()
    {
        $this->dropTable('mvp');
    }
}

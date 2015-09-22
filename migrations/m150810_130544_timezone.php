<?php
use yii\db\Schema;
use yii\db\Migration;

class m150810_130544_timezone extends Migration
{
    public function up()
    {
        $this->createTable('timezone', [
            'id' => $this->primaryKey(),
            'zone' => $this->string(64)->notNull(),
        ]);
        $this->createIndex('ix_timezone_1', 'timezone', 'zone', true);
    }

    public function down()
    {
        $this->dropTable('timezone');
    }
}

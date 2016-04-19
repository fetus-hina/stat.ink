<?php
use yii\db\Migration;

class m160419_133036_battle_edit_history extends Migration
{
    public function up()
    {
        $this->createTable('battle_edit_history', [
            'id'        => $this->bigPrimaryKey(),
            'battle_id' => $this->bigInteger()->notNull(),
            'diff'      => $this->text()->notNull(),
            'at'        => 'TIMESTAMP(0) WITH TIME ZONE',
        ]);
        $this->createIndex('ix_battle_edit_history_1', 'battle_edit_history', 'battle_id');
    }

    public function down()
    {
        $this->dropTable('battle_edit_history');
    }
}

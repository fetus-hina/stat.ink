<?php
use yii\db\Migration;

class m151023_063540_user_stat extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user_stat}} ADD COLUMN [[total_kd_battle_count]] BIGINT NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user_stat}} DROP COLUMN [[total_kd_battle_count]]');
    }
}

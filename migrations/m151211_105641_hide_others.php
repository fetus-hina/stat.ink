<?php
use yii\db\Migration;

class m151211_105641_hide_others extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user}} ADD COLUMN [[is_black_out_others]] BOOLEAN DEFAULT FALSE');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user}} DROP COLUMN [[is_black_out_others]]');
    }
}

<?php
use yii\db\Migration;

class m151217_071059_map_release extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{map}} ADD COLUMN [[release_at]] TIMESTAMP(0) WITH TIME ZONE NULL');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{map}} DROP COLUMN [[release_at]]');
    }
}

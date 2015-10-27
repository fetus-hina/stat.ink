<?php
use yii\db\Migration;

class m151027_113719_ua_custom_data extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[ua_custom]] TEXT');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[ua_custom]]');
    }
}

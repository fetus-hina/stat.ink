<?php
use yii\db\Migration;

class m160409_072631_ua_variables extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ADD COLUMN [[ua_variables]] jsonb');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN [[ua_variables]]');
    }
}

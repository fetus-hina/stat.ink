<?php
use yii\db\Migration;

class m151120_183237_events extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{battle}} ADD COLUMN {{events}} JSONB');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{battle}} DROP COLUMN {{events}}');
    }
}

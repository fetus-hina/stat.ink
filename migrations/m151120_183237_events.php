<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

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

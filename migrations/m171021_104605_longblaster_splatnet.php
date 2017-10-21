<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m171021_104605_longblaster_splatnet extends Migration
{
    public function safeUp()
    {
        $this->update('weapon2', ['splatnet' => 220], ['key' => 'longblaster']);
    }

    public function safeDown()
    {
        $this->update('weapon2', ['splatnet' => null], ['key' => 'longblaster']);
    }
}

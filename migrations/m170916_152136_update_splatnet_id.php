<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170916_152136_update_splatnet_id extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['splatnet' => 10], ['key' => 'mozuku']);
        $this->update('weapon2', ['splatnet' => 4011], ['key' => 'barrelspinner_deco']);
    }

    public function safeDown()
    {
        $this->update('map2', ['splatnet' => null], ['key' => 'mozuku']);
        $this->update('weapon2', ['splatnet' => null], ['key' => 'barrelspinner_deco']);
    }
}

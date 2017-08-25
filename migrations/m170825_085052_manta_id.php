<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m170825_085052_manta_id extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['splatnet' => 6], ['key' => 'manta']);
    }

    public function safeDown()
    {
        $this->update('map2', ['splatnet' => null], ['key' => 'manta']);
    }
}

<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180111_155313_devon extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['splatnet' => 12], ['key' => 'devon']);
    }

    public function safeDown()
    {
        $this->update('map2', ['splatnet' => null], ['key' => 'devon']);
    }
}

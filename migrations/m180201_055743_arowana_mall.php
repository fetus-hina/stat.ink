<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180201_055743_arowana_mall extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['splatnet' => 15], ['key' => 'arowana']);
    }

    public function safeDown()
    {
        $this->update('map2', ['splatnet' => null], ['key' => 'arowana']);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171222_070039_splatnetid extends Migration
{
    public function safeUp()
    {
        $this->update('weapon2', ['splatnet' => 400], ['key' => 'bottlegeyser']);
    }

    public function safeDown()
    {
        $this->update('weapon2', ['splatnet' => null], ['key' => 'bottlegeyser']);
    }
}

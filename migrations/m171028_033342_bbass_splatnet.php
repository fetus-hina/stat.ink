<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171028_033342_bbass_splatnet extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            ['splatnet' => 11],
            ['key' => 'bbass'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            ['splatnet' => null],
            ['key' => 'bbass'],
        );
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180425_175926_rank_x_base extends Migration
{
    public function safeUp()
    {
        $this->update('rank2', ['int_base' => 1100], ['key' => 'x']);
    }

    public function safeDown()
    {
        $this->update('rank2', ['int_base' => 1010], ['key' => 'x']);
    }
}

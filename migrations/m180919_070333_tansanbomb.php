<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180919_070333_tansanbomb extends Migration
{
    public function safeUp()
    {
        $this->insert('subweapon2', [
            'key' => 'tansanbomb',
            'name' => 'Fizzy Bomb',
        ]);
    }

    public function safeDown()
    {
        $this->delete('subweapon2', ['key' => 'tansanbomb']);
    }
}

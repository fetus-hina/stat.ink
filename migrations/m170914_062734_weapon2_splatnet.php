<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170914_062734_weapon2_splatnet extends Migration
{
    public function safeUp()
    {
        foreach ($this->getList() as $key => $id) {
            $this->update('weapon2', ['splatnet' => $id], ['key' => $key]);
        }
    }

    public function safeDown()
    {
        $this->update('weapon2', ['splatnet' => null], ['key' => array_keys($this->getList())]);
    }

    private function getList(): array
    {
        return [
            'squiclean_a' => 2000,
            'prime_collabo' => 71,
            'screwslosher' => 3020,
            'momiji' => 11,
        ];
    }
}

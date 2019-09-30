<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180713_194549_anocho_bit_games extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'anchovy',
            'name' => 'Ancho-V Games',
            'short_name' => 'Games',
            'area' => null,
            'splatnet' => 21,
            'release_at' => null,
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', [
            'key' => 'anchovy',
        ]);
    }
}

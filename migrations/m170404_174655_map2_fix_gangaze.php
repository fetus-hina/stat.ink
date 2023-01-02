<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170404_174655_map2_fix_gangaze extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            [
                'name' => 'Starfish Mainstage',
                'short_name' => 'Mainstage',
            ],
            [
                'key' => 'gangaze',
            ],
        );
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            [
                'name' => 'Diadema Amphitheater',
                'short_name' => 'Amphitheater',
            ],
            [
                'key' => 'gangaze',
            ],
        );
    }
}

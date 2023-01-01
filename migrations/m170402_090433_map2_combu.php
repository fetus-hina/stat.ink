<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170402_090433_map2_combu extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'combu',
            'name' => 'Humpback Pump Track',
            'short_name' => 'Track',
            'area' => null,
            'release_at' => null,
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'combu']);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170704_172050_stage_ama extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'ama',
            'name' => 'Inkblot Art Academy',
            'short_name' => 'Academy',
            'area' => null,
            'release_at' => null,
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'ama']);
    }
}

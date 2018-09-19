<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180919_071330_stage_mutsugoro extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'mutsugoro',
            'name' => 'ムツゴ楼',
            'short_name' => 'ムツゴ楼',
            'area' => null,
            'splatnet' => null,
            'release_at' => null,
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', [
            'key' => 'mutsugoro',
        ]);
    }
}

<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180731_075129_update_stages extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['release_at' => '2018-08-01T00:00:00+00:00'], ['key' => 'anchovy']);
        $this->update('map2', ['area' => 2858], ['key' => 'sumeshi']);
        $this->update('map2', ['area' => 2405], ['key' => 'otoro']);
    }

    public function safeDown()
    {
        $this->update('map2', ['release_at' => null], ['key' => 'anchovy']);
        $this->update('map2', ['area' => null], ['key' => ['sumeshi', 'otoro']]);
    }
}

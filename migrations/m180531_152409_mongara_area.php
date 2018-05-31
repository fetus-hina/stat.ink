<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180531_152409_mongara_area extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['area' => 2338], ['key' => 'mongara']);
    }

    public function safeDown()
    {
        $this->update('map2', ['area' => null], ['key' => 'mongara']);
    }
}

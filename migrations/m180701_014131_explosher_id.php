<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m180701_014131_explosher_id extends Migration
{
    public function safeUp()
    {
        $this->update('weapon2', ['splatnet' => 3040], ['key' => 'explosher']);
    }

    public function safeDown()
    {
        $this->update('weapon2', ['splatnet' => 3030], ['key' => 'explosher']);
    }
}

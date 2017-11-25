<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;

class m171125_112304_devon extends Migration
{
    public function safeUp()
    {
        $this->insert('map2', [
            'key' => 'devon',
            'name' => 'Shellendorf Institute',
            'short_name' => 'Institute',
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', ['key' => 'devon']);
    }
}

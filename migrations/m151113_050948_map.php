<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151113_050948_map extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map', ['key', 'name'], [
            ['kinmedai', 'Museum d\'Alfonsino'],
            ['mahimahi', 'Mahi-Mahi Resort'],
        ]);
    }

    public function safeDown()
    {
        $this->delete('map', ['key' => ['kinmedai', 'mahimahi']]);
    }
}

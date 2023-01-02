<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m160119_132855_fix_cause_of_d extends Migration
{
    public function safeUp()
    {
        $this->update('death_reason', ['name' => 'Bamboozler 14 Mk I'], ['key' => 'bamboo14mk1']);
    }

    public function safeDown()
    {
        $this->update('death_reason', ['name' => 'Bamboozler 14 MK I'], ['key' => 'bamboo14mk1']);
    }
}

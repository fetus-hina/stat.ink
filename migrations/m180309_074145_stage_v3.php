<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180309_074145_stage_v3 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map2', ['key', 'name', 'short_name'], [
            ['shottsuru', 'Piranha Pit', 'Pit'],
            ['mongara', 'Camp Triggerfish', 'Camp'],
            ['sumeshi', 'Wahoo World', 'World'],
        ]);
    }

    public function safeDown()
    {
        $this->batchInsert('map2', ['key'], [
            ['shottsuru'],
            ['mongara'],
            ['sumeshi'],
        ]);
    }
}

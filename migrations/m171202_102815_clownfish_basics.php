<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171202_102815_clownfish_basics extends Migration
{
    public function safeUp()
    {
        $this->update(
            'gear2',
            [
                'key' => 'clownfish_basics',
                'name' => 'Clownfish Basics',
            ],
            [
                'key' => 'clowfish_basics',
            ],
        );
    }

    public function safeDown()
    {
        $this->update(
            'gear2',
            [
                'key' => 'clowfish_basics',
                'name' => 'Clowfish Basics',
            ],
            [
                'key' => 'clownfish_basics',
            ],
        );
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190626_180253_iidabomb extends Migration
{
    public function safeUp()
    {
        $this->update(
            'death_reason2',
            ['name' => 'Hyperbomb'],
            ['key' => 'iidabomb'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'death_reason2',
            ['name' => 'Marina Bomb'],
            ['key' => 'iidabomb'],
        );
    }
}

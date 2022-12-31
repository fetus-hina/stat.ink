<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200823_190217_inksplode extends Migration
{
    public function safeUp()
    {
        $this->update(
            'death_reason',
            ['name' => 'Rainmaker Inksplosion'],
            ['key' => 'hoko_inksplode'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'death_reason',
            ['name' => 'Rainmaker Inksplode'],
            ['key' => 'hoko_inksplode'],
        );
    }
}

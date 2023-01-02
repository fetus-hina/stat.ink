<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m200409_174248_rename_australia_zone extends Migration
{
    public function safeUp()
    {
        $this->update(
            'timezone_group',
            ['name' => 'Australia'],
            ['name' => 'Australia/Oceania'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'timezone_group',
            ['name' => 'Australia/Oceania'],
            ['name' => 'Australia'],
        );
    }
}

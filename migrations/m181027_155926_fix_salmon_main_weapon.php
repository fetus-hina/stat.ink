<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181027_155926_fix_salmon_main_weapon extends Migration
{
    public function safeUp()
    {
        $this->update(
            'salmon_main_weapon2',
            [
                'key' => 'promodeler_mg',
                'name' => 'Aerospray MG',
                'splatnet' => 30,
            ],
            [
                'key' => 'promodeler_rg',
            ],
        );
    }

    public function safeDown()
    {
        $this->update(
            'salmon_main_weapon2',
            [
                'key' => 'promodeler_rg',
                'name' => 'Aerospray RG',
                'splatnet' => 31,
            ],
            [
                'key' => 'promodeler_mg',
            ],
        );
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\SalmonWeaponMigration;

class m181103_124811_salmon_scope extends Migration
{
    use SalmonWeaponMigration;

    public function safeUp()
    {
        $this->upSalmonWeapons2([
            'splatscope',
            'liter4k_scope',
        ]);
    }

    public function safeDown()
    {
        $this->downSalmonWeapons2([
            'splatscope',
            'liter4k_scope',
        ]);
    }
}

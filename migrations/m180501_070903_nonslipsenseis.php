<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180501_070903_nonslipsenseis extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->upGear2(
            static::name2key('Non-slip Senseis'),
            'Non-slip Senseis',
            'shoes',
            static::name2key('Grizzco'),
            null,
            21002,
        );
    }

    public function safeDown()
    {
        $this->downGear2(
            static::name2key('Non-slip Senseis'),
        );
    }
}

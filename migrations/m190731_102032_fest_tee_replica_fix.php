<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190731_102032_fest_tee_replica_fix extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $this->update(
            'gear2',
            ['splatnet' => 23000],
            ['key' => static::name2key('Splatfest Tee Replica')],
        );
    }

    public function safeDown()
    {
        $this->update(
            'gear2',
            ['splatnet' => 26001], // wrong
            ['key' => static::name2key('Splatfest Tee Replica')],
        );
    }
}

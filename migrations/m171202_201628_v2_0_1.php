<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m171202_201628_v2_0_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '2.0',
            '2.0.x',
            '2.0.1',
            '2.0.1',
            new DateTimeImmutable('2017-12-01T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('2.0.1', '2.0.0');
    }
}

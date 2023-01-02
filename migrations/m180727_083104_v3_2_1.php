<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180727_083104_v3_2_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '3.2',
            '3.2.x',
            '3.2.1',
            '3.2.1',
            new DateTimeImmutable('2018-07-27T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('3.2.1', '3.2.0');
    }
}

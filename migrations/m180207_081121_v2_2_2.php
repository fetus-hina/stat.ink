<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180207_081121_v2_2_2 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '2.2',
            '2.2.x',
            '2.2.2',
            '2.2.2',
            new DateTimeImmutable('2018-02-07T17:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('2.2.2', '2.2.1');
    }
}

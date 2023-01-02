<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180614_153323_v3_1_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '3.1',
            '3.1.x',
            '3.1.0',
            '3.1.0',
            new DateTimeImmutable('2018-06-14T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('3.1.0', '3.0.1');
    }
}

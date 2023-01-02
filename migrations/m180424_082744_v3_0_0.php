<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180424_082744_v3_0_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '3.0',
            '3.0.x',
            '3.0.0',
            '3.0.0',
            new DateTimeImmutable('2018-04-25T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('3.0.0', '2.3.3');
    }
}

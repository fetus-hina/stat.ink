<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180914_064256_version4 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.0',
            '4.0.x',
            '4.0.0',
            '4.0.0',
            new DateTimeImmutable('2018-09-14T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.0.0', '3.2.2');
    }
}

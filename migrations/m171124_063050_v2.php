<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m171124_063050_v2 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '2.0',
            '2.0.x',
            '2.0.0',
            '2.0.0',
            new DateTimeImmutable('2017-11-24T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('2.0.0', '1.4.2');
    }
}

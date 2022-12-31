<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180119_072140_v2_2_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '2.2',
            '2.2.x',
            '2.2.1',
            '2.2.1',
            new DateTimeImmutable('2018-01-19T15:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('2.2.1', '2.2.0');
    }
}

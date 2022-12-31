<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m180314_141132_v2_3_2 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '2.3',
            '2.3.x',
            '2.3.2',
            '2.3.2',
            new DateTimeImmutable('2018-03-14T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('2.3.2', '2.3.1');
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m190717_092015_s2_v4_9_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.9',
            '4.9.x',
            '4.9.1',
            '4.9.1',
            new DateTimeImmutable('2019-07-18T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.9.1', '4.9.0');
    }
}

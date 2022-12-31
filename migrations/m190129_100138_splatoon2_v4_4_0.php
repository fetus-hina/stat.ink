<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m190129_100138_splatoon2_v4_4_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.4',
            '4.4.x',
            '4.4.0',
            '4.4.0',
            new DateTimeImmutable('2019-01-30T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.4.0', '4.3.1');
    }
}

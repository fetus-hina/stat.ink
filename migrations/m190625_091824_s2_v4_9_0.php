<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m190625_091824_s2_v4_9_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.9',
            '4.9.x',
            '4.9.0',
            '4.9.0',
            new DateTimeImmutable('2019-06-26T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.9.0', '4.8.0');
    }
}

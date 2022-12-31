<?php

/**
 * @copyright Copyright (C) 2015-2020 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m201028_052219_s2_v5_3_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '5.3',
            '5.3.x',
            '5.3.1',
            '5.3.1',
            new DateTimeImmutable('2020-10-28T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('5.3.1', '5.3.0');
    }
}

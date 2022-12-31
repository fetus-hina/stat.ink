<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m191010_001636_s2_v_5_0_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '5.0',
            '5.0.x',
            '5.0.1',
            '5.0.1',
            new DateTimeImmutable('2019-10-10T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('5.0.1', '5.0.0');
    }
}

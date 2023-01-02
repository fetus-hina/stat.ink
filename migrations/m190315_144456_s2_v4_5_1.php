<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m190315_144456_s2_v4_5_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.5',
            '4.5.x',
            '4.5.1',
            '4.5.1',
            new DateTimeImmutable('2019-03-15T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.5.1', '4.5.0');
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m181002_181227_s2v4_1 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.1',
            '4.1.x',
            '4.1.0',
            '4.1.0',
            new DateTimeImmutable('2018-10-03T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.1.0', '4.0.0');
    }
}

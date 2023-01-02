<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m190423_100011_splatoon2_4_7_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.7',
            '4.7.x',
            '4.7.0',
            '4.7.0',
            new DateTimeImmutable('2019-04-24T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.7.0', '4.6.1');
    }
}

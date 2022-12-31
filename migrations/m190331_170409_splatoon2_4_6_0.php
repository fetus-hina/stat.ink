<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m190331_170409_splatoon2_4_6_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.6',
            '4.6.x',
            '4.6.0',
            '4.6.0',
            new DateTimeImmutable('2019-04-03T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.6.0', '4.5.1');
    }
}

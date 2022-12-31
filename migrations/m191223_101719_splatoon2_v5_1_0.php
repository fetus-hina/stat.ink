<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m191223_101719_splatoon2_v5_1_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '5.1',
            '5.1.x',
            '5.1.0',
            '5.1.0',
            new DateTimeImmutable('2020-01-06T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('5.1.0', '5.0.1');
    }
}

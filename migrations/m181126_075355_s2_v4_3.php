<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m181126_075355_s2_v4_3 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '4.3',
            '4.3.x',
            '4.3.0',
            '4.3.0',
            new DateTimeImmutable('2018-12-05T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('4.3.0', '4.2.0');
    }
}

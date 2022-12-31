<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m210924_081613_splatoon2_5_5_0 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2(
            '5.5',
            '5.5.x',
            '5.5.0',
            '5.5.0',
            new DateTimeImmutable('2021-09-29T11:00:00+09:00'),
        );
    }

    public function safeDown()
    {
        $this->downVersion2('5.5.0', '5.4.0');
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\VersionMigration;

class m181101_073835_splatoon2_v4_2 extends Migration
{
    use VersionMigration;

    public function safeUp()
    {
        $this->upVersion2('4.2', '4.2.x', '4.2.0', '4.2.0', new DateTimeImmutable('2018-11-07T11:00:00+09:00'));
        $this->insert('special2', [
            'key' => 'ultrahanko',
            'name' => 'Ultra Stamp',
        ]);
        $this->insert('subweapon2', [
            'key' => 'torpedo',
            'name' => 'Torpedo',
        ]);
    }

    public function safeDown()
    {
        $this->delete('subweapon2', ['key' => 'torpedo']);
        $this->delete('special2', ['key' => 'ultrahanko']);
        $this->downVersion2('4.2.0', '4.1.0');
    }
}

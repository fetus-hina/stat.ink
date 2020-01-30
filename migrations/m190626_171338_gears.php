<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190626_171338_gears extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        foreach ($this->getGears() as $gearData) {
            call_user_func_array([$this, 'upGear2'], $gearData);
        }
    }

    public function safeDown()
    {
        foreach ($this->getGears() as $gearData) {
            $this->downGear2($gearData[0]);
        }
    }

    public function getGears(): array
    {
        return [
            [
                static::name2key('Worker\'s Cap'),
                'Worker\'s Cap',
                'headgear',
                'grizzco',
                null,
                21008,
            ],
            [
                static::name2key('Jetflame Crest'),
                'Jetflame Crest',
                'headgear',
                'squidforce',
                'run_speed_up',
                24012,
            ],
            [
                static::name2key('Fierce Fishskull'),
                'Fierce Fishskull',
                'headgear',
                'squidforce',
                'swim_speed_up',
                24013,
            ],
            [
                static::name2key('Hivemind Antenna'),
                'Hivemind Antenna',
                'headgear',
                'squidforce',
                'comeback',
                24014,
            ],
            [
                static::name2key('Eye of Justice'),
                'Eye of Justice',
                'headgear',
                'squidforce',
                'special_charge_up',
                24015,
            ],
        ];
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190501_185534_srl_coat extends Migration
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
                static::name2key('SRL Coat'),
                'SRL Coat',
                'clothing',
                'grizzco',
                null,
                21010,
            ],
        ];
    }
}

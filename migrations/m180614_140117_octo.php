<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\GearMigration;
use app\components\db\Migration;

class m180614_140117_octo extends Migration
{
    use GearMigration;

    public function safeUp()
    {
        $list = [
            'headgear' => [
                'Octoling Shades' => 27104,
                'Null Visor Replica' => 27105,
                'Old-Timey Hat' => 27106,
                'Conductor Cap' => 27107,
                'Golden Toothpick' => 27108,
            ],
            'clothing' => [
                'Neo Octoling Armor' => 27104,
                'Null Armor Replica' => 27105,
                'Octoleet Armor' => 27106,
            ],
            'shoes' => [
                'Octoleet Boots' => 21003,
                'Neo Octoling Boots' => 27104,
                'Null Boots Replica' => 27105,
                'Old-Timey Shoes' => 27106,
            ],
        ];

        foreach ($list as $type => $gears) {
            foreach ($gears as $gearName => $gearId) {
                $this->upGear2(
                    static::name2key($gearName),
                    $gearName,
                    $type,
                    static::name2key('Cuttlegear'), // maybe wrong
                    null, // primary
                    $gearId,
                );
            }
        }
    }

    public function safeDown()
    {
        $list = [
            'Conductor Cap',
            'Golden Toothpick',
            'Neo Octoling Armor',
            'Neo Octoling Boots',
            'Null Armor Replica',
            'Null Boots Replica',
            'Null Visor Replica',
            'Octoleet Armor',
            'Octoleet Boots',
            'Octoling Shades',
            'Old-Timey Hat',
            'Old-Timey Shoes',
        ];

        foreach ($list as $name) {
            $this->downGear2(static::name2key($name));
        }
    }
}

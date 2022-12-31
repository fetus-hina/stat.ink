<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190401_012905_spring_fest extends Migration
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
        return array_merge(
            $this->getHeadgears(),
            $this->getShoes(),
        );
    }

    private function getHeadgears(): array
    {
        return array_map(
            fn (string $name): array => [
                    static::name2key($name),
                    $name,
                    'headgear',
                    'squidforce', // not verified
                    null, // ability
                    null, // splatnet
                ],
            [
                'Orange Novelty Visor',
                'Pink Novelty Visor',
                'Purple Novelty Visor',
                'Green Novelty Visor',
            ],
        );
    }

    private function getShoes(): array
    {
        return array_map(
            fn (string $name): array => [
                    static::name2key($name),
                    $name,
                    'shoes',
                    'squidforce', // not verified
                    null, // ability
                    null, // splatnet
                ],
            [
                'Pearl-Scout Lace-Ups',
                'Pearlescent Squidkid IV',
                'Marination Lace-Ups',
                'Rina Squidkid IV',
                'Pearl Punk Crowns',
                'New-Day Arrows',
                'Trooper Power Stripes',
                'Midnight Slip-Ons',
            ],
        );
    }
}

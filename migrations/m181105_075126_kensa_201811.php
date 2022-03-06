<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m181105_075126_kensa_201811 extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        foreach ($this->getWeapons() as $weaponData) {
            call_user_func_array([$this, 'upWeapon'], $weaponData);
        }
    }

    public function safeDown()
    {
        foreach ($this->getWeapons() as $weaponData) {
            $this->downWeapon($weaponData[0]);
        }
    }

    public function getWeapons(): array
    {
        return [
            ['ochiba', 'Kensa Splattershot Jr.', 'shooter', 'torpedo', 'bubble', 'wakaba', null, 12],
            ['hokusai_becchu', 'Kensa Octobrush', 'brush', 'kyubanbomb', 'ultrahanko', 'hokusai', null, 1112],
            ['spygadget_becchu', 'Kensa Undercover Brella', 'brella', 'torpedo', 'armor', 'spygadget', null, 6022],
            [
                'l3reelgun_becchu',
                'Kensa L-3 Nozzlenose',
                'reelgun',
                'splashshield',
                'ultrahanko',
                'l3reelgun',
                null,
                302,
            ],
        ];
    }
}

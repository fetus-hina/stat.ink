<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m190331_160257_sheldon extends Migration
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
            [
                'sputtery_clear',
                'Clear Dapple Dualies',
                'maneuver',
                'torpedo',
                'chakuchi',
                'sputtery',
                null,
                -2,
            ],
            [
                'campingshelter_camo',
                'Tenta Camo Brella',
                'brella',
                'trap',
                'ultrahanko',
                'campingshelter',
                null,
                -2,
            ],
            [
                'squiclean_g',
                'Fresh Squiffer',
                'charger',
                'kyubanbomb',
                'jetpack',
                'squiclean_a',
                null,
                -2,
            ],
            [
                'pablo_permanent',
                'Permanent Inkbrush',
                'brush',
                'sprinkler',
                'armor',
                'pablo',
                null,
                -2,
            ],
            [
                'bucketslosher_soda',
                'Soda Slosher',
                'slosher',
                'splashbomb',
                'pitcher',
                'bucketslosher',
                null,
                -2,
            ],
            [
                'barrelspinner_remix',
                'Heavy Splatling Remix',
                'splatling',
                'pointsensor',
                'nicedama',
                'barrelspinner',
                null,
                -2,
            ],
            [
                'bamboo14mk3',
                'Bamboozler 14 Mk III',
                'charger',
                'tansanbomb',
                'bubble',
                'bamboo14mk1',
                null,
                -2,
            ],
            [
                'longblaster_necro',
                'Grim Range Blaster',
                'blaster',
                'quickbomb',
                'missile',
                'longblaster',
                null,
                -2,
            ],
            [
                'nzap83',
                "N-ZAP '83",
                'shooter',
                'sprinkler',
                'amefurashi',
                'nzap85',
                null,
                -2,
            ],
            [
                'promodeler_pg',
                'Aerospray PG',
                'shooter',
                'quickbomb',
                'nicedama',
                'promodeler_mg',
                null,
                -2,
            ],
            [
                'bold_7',
                'Sploosh-o-matic 7',
                'shooter',
                'splashbomb',
                'ultrahanko',
                'bold',
                null,
                -2,
            ],
            [
                'h3reelgun_cherry',
                'Cherry H-3 Nozzlenose',
                'reelgun',
                'splashshield',
                'bubble',
                'h3reelgun',
                null,
                -2,
            ],
        ];
    }
}

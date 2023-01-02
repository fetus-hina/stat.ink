<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m170804_152052_weapon2_hero extends Migration
{
    use WeaponMigration;

    public function safeUp()
    {
        $this->upWeapon(
            'heroshooter_replica',
            'Hero Shot Replica',
            'shooter',
            'quickbomb',
            'chakuchi',
            'sshooter',
            'sshooter',
        );
        $this->upWeapon(
            'heroblaster_replica',
            'Hero Blaster Replica',
            'blaster',
            'poisonmist',
            'chakuchi',
            'hotblaster',
            'hotblaster',
        );
        $this->upWeapon(
            'heromaneuver_replica',
            'Hero Dualies Replica',
            'maneuver',
            'quickbomb',
            'missile',
            'maneuver',
            'maneuver',
        );
        $this->upWeapon(
            'herocharger_replica',
            'Hero Charger Replica',
            'charger',
            'splashbomb',
            'presser',
            'splatcharger',
            'splatcharger',
        );
        $this->upWeapon(
            'heroroller_replica',
            'Hero Roller Replica',
            'roller',
            'curlingbomb',
            'chakuchi',
            'splatroller',
            'splatroller',
        );
        $this->upWeapon(
            'herobrush_replica',
            'Hero Brush Replica',
            'brush',
            'robotbomb',
            'jetpack',
            'hokusai',
            'hokusai',
        );
        $this->upWeapon(
            'heroslosher_replica',
            'Hero Slosher Replica',
            'slosher',
            'kyubanbomb',
            'missile',
            'bucketslosher',
            'bucketslosher',
        );
        $this->upWeapon(
            'herospinner_replica',
            'Hero Splatling Replica',
            'splatling',
            'sprinkler',
            'presser',
            'barrelspinner',
            'barrelspinner',
        );
    }

    public function safeDown()
    {
        $this->downWeapon('heroshooter_replica');
        $this->downWeapon('heroblaster_replica');
        $this->downWeapon('heromaneuver_replica');
        $this->downWeapon('herocharger_replica');
        $this->downWeapon('heroroller_replica');
        $this->downWeapon('herobrush_replica');
        $this->downWeapon('heroslosher_replica');
        $this->downWeapon('herospinner_replica');
    }
}

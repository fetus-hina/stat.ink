<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use app\components\db\Migration;
use app\components\db\WeaponMigration;

class m180629_171412_june_weapons extends Migration
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

    public function getWeapons() : array
    {
        return [
            ['bamboo14mk2', 'Bamboozler 14 Mk II', 'charger', 'poisonmist', 'pitcher', 'bamboo14mk1', null, 2051],
            ['campingshelter_sorella', 'Tenta Sorella Brella', 'brella', 'splashshield', 'pitcher', 'campingshelter', null, 6011],
            ['kugelschreiber', 'Ballpoint Splatling', 'splatling', 'poisonmist', 'jetpack', null, null, 4030],
            ['explosher', 'Explosher', 'slosher', 'sprinkler', 'bubble', null, null, 3030],
        ];
    }
}

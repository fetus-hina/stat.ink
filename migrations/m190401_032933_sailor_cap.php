<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */
declare(strict_types=1);

use app\components\db\GearMigration;
use app\components\db\Migration;

class m190401_032933_sailor_cap extends Migration
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
        // types: headgear, clothing, shoes
        // brands: amiibo, annaki, cuttlegear, enperry, firefin, forge, grizzco, inkline, krak_on, rockenberg, skalop, splash_mob, squidforce, takoroka, tentatek, toni_kensa, zekko, zink
        // abilities: ability_doubler, bomb_defense_up, bomb_defense_up_dx, cold_blooded, comeback, drop_roller, haunt, ink_recovery_up, ink_resistance_up, ink_saver_main, ink_saver_sub, last_ditch_effort, main_power_up, ninja_squid, object_shredder, opening_gambit, quick_respawn, quick_super_jump, respawn_punisher, run_speed_up, special_charge_up, special_power_up, special_saver, stealth_jump, sub_power_up, swim_speed_up, tenacity, thermal_ink
        return [
            [
                static::name2key('Sailor Cap'),
                'Sailor Cap',
                'headgear',
                'grizzco',
                null,
                21009,
            ],
        ];
    }
}

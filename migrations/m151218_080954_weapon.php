<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\DeathReasonType;
use app\models\Special;
use app\models\Subweapon;
use app\models\WeaponType;
use yii\db\Migration;

class m151218_080954_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'slosher'])->id,
            'key' => 'hissen_hue',
            'name' => 'Tri-Slosher Nouveau',
            'subweapon_id' => Subweapon::findOne(['key' => 'chasebomb'])->id,
            'special_id' => Special::findOne(['key' => 'supersensor'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'hissen_hue',
            'name' => 'Tri-Slosher Nouveau',
        ]);

        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'shooter'])->id,
            'key' => 'rapid_elite_deco',
            'name' => 'Rapid Blaster Pro Deco',
            'subweapon_id' => Subweapon::findOne(['key' => 'poison'])->id,
            'special_id' => Special::findOne(['key' => 'megaphone'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'rapid_elite_deco',
            'name' => 'Rapid Blaster Pro Deco',
        ]);
    }

    public function safeDown()
    {
        $keys = [
            'hissen_hue',
            'rapid_elite_deco',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

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

class m151016_062505_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'shooter'])->id,
            'key' => 'rapid_elite',
            'name' => 'Rapid Blaster Pro',
            'subweapon_id' => Subweapon::findOne(['key' => 'chasebomb'])->id,
            'special_id' => Special::findOne(['key' => 'supershot'])->id,
        ]);

        $mainWeapon = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->insert('death_reason', [
            'type_id' => $mainWeapon,
            'key' => 'rapid_elite',
            'name' => 'Rapid Blaster Pro',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'rapid_elite']);
        $this->delete('weapon', ['key' => 'rapid_elite']);
    }
}

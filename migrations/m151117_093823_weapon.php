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

class m151117_093823_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'charger'])->id,
            'key' => 'bamboo14mk2',
            'name' => 'Bamboozler 14 Mk II',
            'subweapon_id' => Subweapon::findOne(['key' => 'poison'])->id,
            'special_id' => Special::findOne(['key' => 'supersensor'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'bamboo14mk2',
            'name' => 'mboozler 14 Mk II',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'bamboo14mk2']);
        $this->delete('weapon', ['key' => 'bamboo14mk2']);
    }
}

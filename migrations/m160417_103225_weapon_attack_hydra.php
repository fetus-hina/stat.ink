<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\SplatoonVersion;
use app\models\Weapon;
use yii\db\Migration;

class m160417_103225_weapon_attack_hydra extends Migration
{
    public function safeUp()
    {
        $version = SplatoonVersion::findOne(['tag' => '2.7.0'])->id;
        $weapon = Weapon::findOne(['key' => 'hydra'])->id;
        $this->insert('weapon_attack', [
            'version_id'        => $version,
            'main_weapon_id'    => $weapon,
            'damage'            => 35.0,
        ]);
    }

    public function safeDown()
    {
        $version = SplatoonVersion::findOne(['tag' => '2.7.0'])->id;
        $weapon = Weapon::findOne(['key' => 'hydra'])->id;
        $this->delete('weapon_attack', [
            'version_id'        => $version,
            'main_weapon_id'    => $weapon,
        ]);
    }
}

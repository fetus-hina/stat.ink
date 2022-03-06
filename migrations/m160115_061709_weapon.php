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

class m160115_061709_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'splatling'])->id,
            'key' => 'hydra_custom',
            'name' => 'Custom Hydra Splatling',
            'subweapon_id' => Subweapon::findOne(['key' => 'sprinkler'])->id,
            'special_id' => Special::findOne(['key' => 'barrier'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'hydra_custom',
            'name' => 'Custom Hydra Splatling',
        ]);
    }

    public function safeDown()
    {
        $keys = [
            'hydra_custom',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

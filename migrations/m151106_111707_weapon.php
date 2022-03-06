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

class m151106_111707_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'splatling'])->id,
            'key' => 'barrelspinner_deco',
            'name' => 'Heavy Splatling Deco',
            'subweapon_id' => Subweapon::findOne(['key' => 'pointsensor'])->id,
            'special_id' => Special::findOne(['key' => 'daioika'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'barrelspinner_deco',
            'name' => 'Heavy Splatling Deco',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'barrelspinner_deco']);
        $this->delete('weapon', ['key' => 'barrelspinner_deco']);
    }
}

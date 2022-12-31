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

class m151124_085332_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'slosher'])->id,
            'key' => 'bucketslosher_deco',
            'name' => 'Slosher Deco',
            'subweapon_id' => Subweapon::findOne(['key' => 'splashshield'])->id,
            'special_id' => Special::findOne(['key' => 'daioika'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'bucketslosher_deco',
            'name' => 'Slosher Deco',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'bucketslosher_deco']);
        $this->delete('weapon', ['key' => 'bucketslosher_deco']);
    }
}

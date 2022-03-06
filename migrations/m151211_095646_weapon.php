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

class m151211_095646_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'splatling'])->id,
            'key' => 'splatspinner_collabo',
            'name' => 'Zink Mini Splatling',
            'subweapon_id' => Subweapon::findOne(['key' => 'poison'])->id,
            'special_id' => Special::findOne(['key' => 'barrier'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'splatspinner_collabo',
            'name' => 'Zink Mini Splatling',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'splatspinner_collabo']);
        $this->delete('weapon', ['key' => 'splatspinner_collabo']);
    }
}

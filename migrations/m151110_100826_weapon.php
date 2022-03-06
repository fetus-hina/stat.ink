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

class m151110_100826_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'shooter'])->id,
            'key' => 'bold_neo',
            'name' => 'Neo Sploosh-o-matic',
            'subweapon_id' => Subweapon::findOne(['key' => 'pointsensor'])->id,
            'special_id' => Special::findOne(['key' => 'daioika'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'bold_neo',
            'name' => 'Neo Sploosh-o-matic',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'bold_neo']);
        $this->delete('weapon', ['key' => 'bold_neo']);
    }
}

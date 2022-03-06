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

class m160108_090118_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'slosher'])->id,
            'key' => 'screwslosher_neo',
            'name' => 'Sloshing Machine Neo',
            'subweapon_id' => Subweapon::findOne(['key' => 'pointsensor'])->id,
            'special_id' => Special::findOne(['key' => 'supershot'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'screwslosher_neo',
            'name' => 'Sloshing Machine Neo',
        ]);
    }

    public function safeDown()
    {
        $keys = [
            'screwslosher_neo',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

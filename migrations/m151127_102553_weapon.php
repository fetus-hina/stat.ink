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

class m151127_102553_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'slosher'])->id,
            'key' => 'screwslosher',
            'name' => 'Sloshing Machine',
            'subweapon_id' => Subweapon::findOne(['key' => 'splashbomb'])->id,
            'special_id' => Special::findOne(['key' => 'bombrush'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'screwslosher',
            'name' => 'Sloshing Machine',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'screwslosher']);
        $this->delete('weapon', ['key' => 'screwslosher']);
    }
}

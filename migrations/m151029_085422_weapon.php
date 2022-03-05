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

class m151029_085422_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'shooter'])->id,
            'key' => 'nova_neo',
            'name' => 'Luna Blaster Neo',
            'subweapon_id' => Subweapon::findOne(['key' => 'splashbomb'])->id,
            'special_id' => Special::findOne(['key' => 'bombrush'])->id,
        ]);

        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'shooter'])->id,
            'key' => 'h3reelgun_d',
            'name' => 'H-3 Nozzlenose D',
            'subweapon_id' => Subweapon::findOne(['key' => 'pointsensor'])->id,
            'special_id' => Special::findOne(['key' => 'supershot'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'nova_neo',
            'name' => 'Luna Blaster Neo',
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'h3reelgun_d',
            'name' => 'H-3 Nozzlenose D',
        ]);
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => ['nova_neo', 'h3reelgun_d']]);
        $this->delete('weapon', ['key' => ['nova_neo', 'h3reelgun_d']]);
    }
}

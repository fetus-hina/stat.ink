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

class m160101_025710_weapon extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'roller'])->id,
            'key' => 'hokusai_hue',
            'name' => 'Octobrush Nouveau',
            'subweapon_id' => Subweapon::findOne(['key' => 'splashbomb'])->id,
            'special_id' => Special::findOne(['key' => 'supershot'])->id,
        ]);

        $this->insert('death_reason', [
            'type_id' => DeathReasonType::findOne(['key' => 'main'])->id,
            'key' => 'hokusai_hue',
            'name' => 'Octobrush Nouveau',
        ]);
    }

    public function safeDown()
    {
        $keys = [
            'hokusai_hue',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

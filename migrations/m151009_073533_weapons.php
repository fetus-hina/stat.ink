<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\Special;
use app\models\Subweapon;
use app\models\WeaponType;
use app\models\DeathReasonType;

class m151009_073533_weapons extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'charger'])->id,
            'key' => 'liter3k_scope_custom',
            'name' => 'Custom E-liter 3K Scope',
            'subweapon_id' => Subweapon::findOne(['key' => 'jumpbeacon'])->id,
            'special_id' => Special::findOne(['key' => 'daioika'])->id,
        ]);

        $this->insert('weapon', [
            'type_id' => WeaponType::findOne(['key' => 'shooter'])->id,
            'key' => 'longblaster_custom',
            'name' => 'Custom Range Blaster',
            'subweapon_id' => Subweapon::findOne(['key' => 'splashbomb'])->id,
            'special_id' => Special::findOne(['key' => 'daioika'])->id,
        ]);

        $mainWeapon = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->batchInsert('death_reason', ['type_id', 'key', 'name'], [
            [ $mainWeapon, 'liter3k_scope_custom', 'Custom E-liter 3K Scope' ],
            [ $mainWeapon, 'longblaster_custom', 'Custom Range Blaster' ],
        ]);
    }

    public function safeDown()
    {
        $this->delete(
            'death_reason',
            ['in', 'key', ['liter3k_scope_custom', 'longblaster_custom']],
        );
        $this->delete(
            'weapon',
            ['in', 'key', ['liter3k_scope_custom', 'longblaster_custom']],
        );
    }
}

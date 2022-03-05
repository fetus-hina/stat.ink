<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\DeathReasonType;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use yii\db\Expression;
use yii\db\Migration;

class m160408_112546_weapon extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            'weapon',
            ['id', 'type_id', 'key', 'name', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'],
            [
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'roller'])->id,
                    'pablo_permanent',
                    'Permanent Inkbrush',
                    Subweapon::findOne(['key' => 'splashbomb'])->id,
                    Special::findOne(['key' => 'daioika'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'pablo'])->id,
                ],
            ]
        );

        $type = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->batchInsert(
            'death_reason',
            ['type_id', 'key', 'name', 'weapon_id'],
            [
                [ $type, 'pablo_permanent', 'Permanent Inkbrush', Weapon::findOne(['key' => 'pablo_permanent'])->id ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => 'pablo_permanent']);
        $this->delete('weapon', ['key' => 'pablo_permanent']);
    }
}

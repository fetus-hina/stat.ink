<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use yii\db\Expression;
use app\models\Special;
use app\models\Subweapon;
use app\models\Weapon;
use app\models\WeaponType;
use app\models\DeathReasonType;

class m160607_015542_weapon_bukichi2_3 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            'weapon',
            ['id', 'type_id', 'key', 'name', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'],
            [
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'shooter'])->id,
                    'promodeler_pg',
                    'Aerospray PG',
                    Subweapon::findOne(['key' => 'quickbomb'])->id,
                    Special::findOne(['key' => 'daioika'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'promodeler_mg'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'splatling'])->id,
                    'barrelspinner_remix',
                    'Heavy Splatling Remix',
                    Subweapon::findOne(['key' => 'splashbomb'])->id,
                    Special::findOne(['key' => 'supershot'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'barrelspinner'])->id,
                ],
            ],
        );

        $type = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->batchInsert(
            'death_reason',
            ['type_id', 'key', 'name', 'weapon_id'],
            [
                [
                    $type,
                    'promodeler_pg',
                    'Aerospray PG',
                    Weapon::findOne(['key' => 'promodeler_mg'])->id,
                ],
                [
                    $type,
                    'barrelspinner_remix',
                    'Heavy Splatling Remix',
                    Weapon::findOne(['key' => 'barrelspinner'])->id,
                ],
            ],
        );
    }

    public function safeDown()
    {
        $keys = [
            'promodeler_pg',
            'barrelspinner_remix',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

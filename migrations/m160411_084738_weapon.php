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

class m160411_084738_weapon extends Migration
{
    public function safeUp()
    {
        $this->batchInsert(
            'weapon',
            ['id', 'type_id', 'key', 'name', 'subweapon_id', 'special_id', 'canonical_id', 'main_group_id'],
            [
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'slosher'])->id,
                    'bucketslosher_soda',
                    'Soda Slosher',
                    Subweapon::findOne(['key' => 'splashbomb'])->id,
                    Special::findOne(['key' => 'supershot'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'bucketslosher'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'charger'])->id,
                    'bamboo14mk3',
                    'Bamboozler 14 Mk III',
                    Subweapon::findOne(['key' => 'quickbomb'])->id,
                    Special::findOne(['key' => 'tornado'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'bamboo14mk1'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'roller'])->id,
                    'dynamo_burned',
                    'Tempered Dynamo Roller',
                    Subweapon::findOne(['key' => 'chasebomb'])->id,
                    Special::findOne(['key' => 'megaphone'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'dynamo'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'splatling'])->id,
                    'splatspinner_repair',
                    'Refurbished Mini Splatling',
                    Subweapon::findOne(['key' => 'quickbomb'])->id,
                    Special::findOne(['key' => 'bombrush'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'splatspinner'])->id,
                ],
            ],
        );

        $type = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->batchInsert(
            'death_reason',
            ['type_id', 'key', 'name', 'weapon_id'],
            [
                [$type, 'bucketslosher_soda', 'Soda Slosher', Weapon::findOne(['key' => 'bucketslosher_soda'])->id],
                [$type, 'bamboo14mk3', 'Bamboozler 14 Mk III', Weapon::findOne(['key' => 'bamboo14mk3'])->id],
                [$type, 'dynamo_burned', 'Tempered Dynamo Roller', Weapon::findOne(['key' => 'dynamo_burned'])->id],
                [
                    $type,
                    'splatspinner_repair',
                    'Refurbished Mini Splatling',
                    Weapon::findOne(['key' => 'splatspinner_repair'])->id,
                ],
            ],
        );
    }

    public function safeDown()
    {
        $keys = [
            'bucketslosher_soda',
            'bamboo14mk3',
            'dynamo_burned',
            'splatspinner_repair',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

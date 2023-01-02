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

class m160603_045703_weapon_bukichi2 extends Migration
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
                    'longblaster_necro',
                    'Grim Range Blaster',
                    Subweapon::findOne(['key' => 'quickbomb'])->id,
                    Special::findOne(['key' => 'megaphone'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'longblaster'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'charger'])->id,
                    'splatcharger_bento',
                    'Bento Splat Charger',
                    Subweapon::findOne(['key' => 'splashshield'])->id,
                    Special::findOne(['key' => 'supersensor'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'splatcharger'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'charger'])->id,
                    'splatscope_bento',
                    'Bento Splatterscope',
                    Subweapon::findOne(['key' => 'splashshield'])->id,
                    Special::findOne(['key' => 'supersensor'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'splatcharger'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'shooter'])->id,
                    'nzap83',
                    'N-ZAP \'83',
                    Subweapon::findOne(['key' => 'pointsensor'])->id,
                    Special::findOne(['key' => 'daioika'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'nzap85'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'roller'])->id,
                    'splatroller_corocoro',
                    'CoroCoro Splat Roller',
                    Subweapon::findOne(['key' => 'splashshield'])->id,
                    Special::findOne(['key' => 'supershot'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'splatroller'])->id,
                ],
            ],
        );

        $type = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->batchInsert(
            'death_reason',
            ['type_id', 'key', 'name', 'weapon_id'],
            [
                [$type, 'longblaster_necro', 'Grim Range Blaster', Weapon::findOne(['key' => 'longblaster_necro'])->id],
                [
                    $type,
                    'splatcharger_bento',
                    'Bento Splat Charger',
                    Weapon::findOne(['key' => 'splatcharger_bento'])->id,
                ],
                [$type, 'splatscope_bento', 'Bento Splatterscope', Weapon::findOne(['key' => 'splatscope_bento'])->id],
                [$type, 'nzap83', 'N-ZAP \'83', Weapon::findOne(['key' => 'nzap83'])->id],
                [
                    $type,
                    'splatroller_corocoro',
                    'CoroCoro Splat Roller',
                    Weapon::findOne(['key' => 'splatroller_corocoro'])->id,
                ],
            ],
        );
    }

    public function safeDown()
    {
        $keys = [
            'longblaster_necro',
            'splatcharger_bento',
            'splatscope_bento',
            'nzap83',
            'splatroller_corocoro',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

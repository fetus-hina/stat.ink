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

class m160606_083853_weapon_bukichi2_2 extends Migration
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
                    'h3reelgun_cherry',
                    'Cherry H-3 Nozzlenose',
                    Subweapon::findOne(['key' => 'splashshield'])->id,
                    Special::findOne(['key' => 'barrier'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'h3reelgun'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'shooter'])->id,
                    'bold_7',
                    'Sploosh-o-matic 7',
                    Subweapon::findOne(['key' => 'splashbomb'])->id,
                    Special::findOne(['key' => 'supershot'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'bold'])->id,
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
                    'h3reelgun_cherry',
                    'Cherry H-3 Nozzlenose',
                    Weapon::findOne(['key' => 'h3reelgun_cherry'])->id,
                ],
                [
                    $type,
                    'bold_7',
                    'Sploosh-o-matic 7',
                    Weapon::findOne(['key' => 'bold'])->id,
                ],
            ],
        );
    }

    public function safeDown()
    {
        $keys = [
            'h3reelgun_cherry',
            'bold_7',
        ];
        $this->delete('death_reason', ['key' => $keys]);
        $this->delete('weapon', ['key' => $keys]);
    }
}

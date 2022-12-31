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

class m160304_132438_weapon extends Migration
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
                    'sshooter_wasabi',
                    'Wasabi Splattershot',
                    Subweapon::findOne(['key' => 'splashbomb'])->id,
                    Special::findOne(['key' => 'tornado'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'sshooter'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'shooter'])->id,
                    'prime_berry',
                    'Berry Splattershot Pro',
                    Subweapon::findOne(['key' => 'kyubanbomb'])->id,
                    Special::findOne(['key' => 'bombrush'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'prime'])->id,
                ],
                [
                    new Expression("nextval('weapon_id_seq'::regclass)"),
                    WeaponType::findOne(['key' => 'charger'])->id,
                    'squiclean_g',
                    'Fresh Squiffer',
                    Subweapon::findOne(['key' => 'kyubanbomb'])->id,
                    Special::findOne(['key' => 'daioika'])->id,
                    new Expression("currval('weapon_id_seq'::regclass)"),
                    Weapon::findOne(['key' => 'squiclean_a'])->id,
                ],
            ]
        );

        $type = DeathReasonType::findOne(['key' => 'main'])->id;
        $this->batchInsert(
            'death_reason',
            ['type_id', 'key', 'name', 'weapon_id'],
            [
                [ $type, 'sshooter_wasabi', 'Wasabi Splattershot', Weapon::findOne(['key' => 'sshooter_wasabi'])->id ],
                [ $type, 'prime_berry', 'Berry Splattershot Pro',Weapon::findOne(['key' => 'prime_berry'])->id ],
                [ $type, 'squiclean_g', 'Fresh Squiffer', Weapon::findOne(['key' => 'squiclean_g'])->id ],
            ]
        );
    }

    public function safeDown()
    {
        $this->delete('death_reason', ['key' => ['sshooter_wasabi', 'prime_berry', 'squiclean_g']]);
        $this->delete('weapon', ['key' => ['sshooter_wasabi', 'prime_berry', 'squiclean_g']]);
    }
}

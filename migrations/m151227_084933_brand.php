<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Ability;
use yii\db\Migration;

class m151227_084933_brand extends Migration
{
    public function up()
    {
        $this->createTable('brand', [
            'id' => $this->primaryKey(),
            'key' => 'VARCHAR(32) NOT NULL UNIQUE',
            'name' => 'VARCHAR(32) NOT NULL',
            'strength_id' => 'INTEGER NULL',
            'weakness_id' => 'INTEGER NULL',
        ]);
        $this->addForeignKey('fk_brand_1', 'brand', 'strength_id', 'ability', 'id');
        $this->addForeignKey('fk_brand_2', 'brand', 'weakness_id', 'ability', 'id');

        $a = $this->abilities;
        $this->batchInsert('brand', ['key', 'name', 'strength_id', 'weakness_id'], [
            [ 'firefin', 'Firefin', $a->ink_saver_sub, $a->ink_recovery_up ],
            [ 'forge', 'Forge', $a->special_duration_up, $a->ink_saver_sub ],
            [ 'inkline', 'Inkline', $a->defense_up, $a->damage_up ],
            [ 'krak_on', 'Krak-On', $a->swim_speed_up, $a->defense_up ],
            [ 'rockenberg', 'Rockenberg', $a->run_speed_up, $a->special_saver ],
            [ 'skalop', 'Skalop', $a->quick_respawn, $a->special_saver ],
            [ 'splash_mob', 'Splash Mob', $a->ink_saver_main, $a->run_speed_up ],
            [ 'squidforce', 'Squidforce', $a->damage_up, $a->ink_saver_main ],
            [ 'takoroka', 'Takoroka', $a->special_charge_up, $a->special_duration_up ],
            [ 'tentatek', 'Tentatek', $a->ink_recovery_up, $a->quick_super_jump ],
            [ 'zekko', 'Zekko', $a->special_saver, $a->special_charge_up ],
            [ 'zink', 'Zink', $a->quick_super_jump, $a->quick_respawn ],
            [ 'amiibo', 'Amiibo', null, null ],
            [ 'cuttlegear', 'Cuttlegear', null, null ],
            [ 'famitsu', 'Famitsu', null, null ],
            [ 'kog', 'KOG', null, null ],
            [ 'squid_girl', 'SQUID GIRL', null, null ],
        ]);
    }

    public function down()
    {
        $this->dropTable('brand');
    }

    public function getAbilities()
    {
        $ret = [];
        foreach (Ability::find()->all() as $a) {
            $ret[$a->key] = $a->id;
        }
        return (object)$ret;
    }
}

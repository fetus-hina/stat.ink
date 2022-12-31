<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class m171103_142752_brand2 extends Migration
{
    public function up()
    {
        $this->createTable('brand2', [
            'id'            => $this->primaryKey(),
            'key'           => $this->apiKey(32),
            'name'          => $this->string(32)->notNull(),
            'strength_id'   => $this->pkRef('ability2')->null(),
            'weakness_id'   => $this->pkRef('ability2')->null(),
        ]);
        $a = $this->getAbilities();
        $this->batchInsert('brand2', ['key', 'name', 'strength_id', 'weakness_id'], [
            ['amiibo', 'amiibo', null, null],
            ['annaki', 'Annaki', $a->cold_blooded, $a->special_saver],
            ['cuttlegear', 'Cuttlegear', null, null],
            ['enperry', 'Enperry', $a->sub_power_up, $a->ink_resistance_up],
            ['firefin', 'Firefin', $a->ink_saver_sub, $a->ink_recovery_up],
            ['forge', 'Forge', $a->special_power_up, $a->ink_saver_sub],
            ['grizzco', 'Grizzco', null, null],
            ['inkline', 'Inkline', $a->bomb_defense_up, $a->cold_blooded],
            ['krak_on', 'Krak-On', $a->swim_speed_up, $a->bomb_defense_up],
            ['rockenberg', 'Rockenberg', $a->run_speed_up, $a->swim_speed_up],
            ['skalop', 'Skalop', $a->quick_respawn, $a->special_saver],
            ['splash_mob', 'Splash Mob', $a->ink_saver_main, $a->run_speed_up],
            ['squidforce', 'SquidForce', $a->ink_resistance_up, $a->ink_saver_main],
            ['takoroka', 'Takoroka', $a->special_charge_up, $a->special_power_up],
            ['tentatek', 'Tentatek', $a->ink_recovery_up, $a->quick_super_jump],
            ['toni_kensa', 'Toni Kensa', $a->cold_blooded, $a->sub_power_up],
            ['zekko', 'Zekko', $a->special_saver, $a->special_charge_up],
            ['zink', 'Zink', $a->quick_super_jump, $a->quick_respawn],
        ]);
        $this->analyze('brand2');
    }

    public function down()
    {
        $this->dropTable('brand2');
    }

    public function getAbilities(): \stdClass
    {
        return (object)ArrayHelper::map(
            (new Query())->select(['id', 'key'])->from('ability2')->all(),
            'key',
            'id',
        );
    }
}

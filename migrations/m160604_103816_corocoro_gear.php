<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\models\Ability;
use app\models\Brand;
use app\models\GearType;
use yii\db\Migration;

class m160604_103816_corocoro_gear extends Migration
{
    public function safeUp()
    {
        $zekko = Brand::findOne(['key' => 'zekko'])->id;
        $this->batchInsert('gear', ['key', 'type_id', 'brand_id', 'name', 'ability_id'], [
            [
                'corocoro_cap',
                GearType::findOne(['key' => 'headgear'])->id,
                $zekko,
                'CoroCoro Cap',
                Ability::findOne(['key' => 'damage_up'])->id,
            ],
            [
                'corocoro_parka',
                GearType::findOne(['key' => 'clothing'])->id,
                $zekko,
                'CoroCoro Parka',
                Ability::findOne(['key' => 'cold_blooded'])->id,
            ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('gear', [
            'key' => [
                'corocoro_cap',
                'corocoro_parka',
            ],
        ]);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170728_091805_weapon_category2_maneuver extends Migration
{
    public function safeUp()
    {
        $this->insert('weapon_type2', [
            'key' => 'maneuver',
            'name' => 'Dualies',
            'category_id' => (new Query())
                ->select('id')
                ->from('weapon_category2')
                ->where(['key' => 'shooter'])
                ->scalar(),
        ]);
        $this->update(
            'weapon2',
            [
                'type_id' => (new Query())
                    ->select('id')
                    ->from('weapon_type2')
                    ->where(['key' => 'maneuver'])
                    ->scalar(),
            ],
            [
                'key' => [
                    'manueuver', 'manueuver_collabo', 'sputtery',
                ],
            ],
        );
    }

    public function safeDown()
    {
        $this->update(
            'weapon2',
            [
                'type_id' => (new Query())
                    ->select('id')
                    ->from('weapon_type2')
                    ->where(['key' => 'shooter'])
                    ->scalar(),
            ],
            [
                'type_id' => (new Query())
                    ->select('id')
                    ->from('weapon_type2')
                    ->where(['key' => 'maneuver'])
                    ->scalar(),
            ],
        );
        $this->delete('weapon_type2', ['key' => 'maneuver']);
    }
}

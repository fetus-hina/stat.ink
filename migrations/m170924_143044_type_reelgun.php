<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use yii\db\Query;

class m170924_143044_type_reelgun extends Migration
{
    public function up()
    {
        $list = [
            'shooter' => 1,
            'blaster' => 2,
            'reelgun' => 3,
            'maneuver' => 4,
            'roller' => 1,
            'brush' => 2,
            'charger' => 1,
            'splatling' => 1,
            'slosher' => 1,
            'brella' => 1,
        ];
        $this->execute('ALTER TABLE {{weapon_type2}} ADD COLUMN [[rank]] INTEGER');
        $transaction = $this->db->beginTransaction();
        $this->insert('weapon_type2', [
            'key' => 'reelgun',
            'name' => 'Nozzlenose',
            'category_id' => (new Query())
                ->select(['id'])
                ->from('weapon_category2')
                ->where(['key' => 'shooter'])
                ->scalar(),
        ]);
        foreach ($list as $key => $rank) {
            $this->update('weapon_type2', ['rank' => $rank], ['key' => $key]);
        }
        $this->update(
            'weapon2',
            ['type_id' =>
                (new Query())
                    ->select('id')
                    ->from('weapon_type2')
                    ->where(['key' => 'reelgun'])
                    ->scalar(),
            ],
            ['key' => ['l3reelgun', 'h3reelgun']],
        );
        $transaction->commit();
        $this->execute('ALTER TABLE {{weapon_type2}} ALTER COLUMN [[rank]] SET NOT NULL');
        $this->createIndex('ix_weapon_type2_1', 'weapon_type2', ['category_id', 'rank'], true);
    }

    public function down()
    {
        $this->update(
            'weapon2',
            ['type_id' =>
                (new Query())
                    ->select('id')
                    ->from('weapon_type2')
                    ->where(['key' => 'shooter'])
                    ->scalar(),
            ],
            ['type_id' =>
                (new Query())
                    ->select('id')
                    ->from('weapon_type2')
                    ->where(['key' => 'reelgun'])
                    ->scalar(),
            ],
        );
        $this->dropIndex('ix_weapon_type2_1', 'weapon_type2');
        $this->delete('weapon_type2', ['key' => 'reelgun']);
        $this->execute('ALTER TABLE {{weapon_type2}} DROP COLUMN [[rank]]');
    }
}

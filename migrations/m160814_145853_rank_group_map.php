<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;
use app\models\RankGroup;

class m160814_145853_rank_group_map extends Migration
{
    public function up()
    {
        $map = [
            'c' => ['c-', 'c', 'c+'],
            'b' => ['b-', 'b', 'b+'],
            'a' => ['a-', 'a', 'a+'],
            's' => ['s', 's+'],
        ];
        $this->execute('ALTER TABLE {{rank}} ADD COLUMN [[group_id]] INTEGER');
        foreach ($map as $groupKey => $rankKeys) {
            $this->update(
                'rank',
                ['group_id' => RankGroup::findOne(['key' => $groupKey])->id],
                ['key' => $rankKeys]
            );
        }
        $this->execute('ALTER TABLE {{rank}} ALTER COLUMN [[group_id]] SET NOT NULL');
        $this->addForeignKey('fk_rank_1', 'rank', 'group_id', 'rank_group', 'id');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{rank}} DROP COLUMN [[group_id]]');
    }
}

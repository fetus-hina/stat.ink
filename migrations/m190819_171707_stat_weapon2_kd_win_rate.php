<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190819_171707_stat_weapon2_kd_win_rate extends Migration
{
    public function safeUp()
    {
        $this->createTable('stat_weapon2_kd_win_rate', [
            'rule_id' => $this->pkRef('rule2')->notNull(),
            'map_id' => $this->pkRef('map2')->notNull(),
            'weapon_type_id' => $this->pkRef('weapon_type2')->notNull(),
            'version_group_id' => $this->pkRef('splatoon_version2')->notNull(),
            'rank_group_id' => $this->pkRef('rank_group2')->notNull(),
            'kill' => $this->bigInteger()->notNull(),
            'death' => $this->bigInteger()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            sprintf('PRIMARY KEY (%s)', implode(', ', [
                'rule_id',
                'weapon_type_id',
                'map_id',
                'version_group_id',
                'rank_group_id',
                'kill',
                'death',
            ])),
        ]);
        foreach (['weapon_type_id', 'map_id', 'version_group_id', 'rank_group_id'] as $key) {
            $this->createIndex(
                "ix_stat_weapon2_kd_win_rate_{$key}",
                'stat_weapon2_kd_win_rate',
                ['rule_id', $key],
            );
        }
    }

    public function safeDown()
    {
        $this->dropTable('stat_weapon2_kd_win_rate');
    }
}

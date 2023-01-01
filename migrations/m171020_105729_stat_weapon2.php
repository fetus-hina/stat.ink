<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171020_105729_stat_weapon2 extends Migration
{
    public function up()
    {
        $this->createTable('stat_weapon2_result', [
            'weapon_id' => $this->pkRef('weapon2'),
            'rule_id' => $this->pkRef('rule2'),
            'map_id' => $this->pkRef('map2'),
            'lobby_id' => $this->pkRef('lobby2'),
            'mode_id' => $this->pkRef('mode2'),
            'rank_id' => $this->pkRef('rank2'),
            'version_id' => $this->pkRef('splatoon_version2'),
            'kill' => $this->bigInteger()->notNull(),
            'death' => $this->bigInteger()->notNull(),
            'assist' => $this->bigInteger()->notNull(),
            'special' => $this->bigInteger()->notNull(),
            'points' => $this->bigInteger()->notNull(),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            sprintf('PRIMARY KEY (%s)', implode(', ', [
                'weapon_id',
                'rule_id',
                'map_id',
                'lobby_id',
                'mode_id',
                'rank_id',
                'version_id',
                'kill',
                'death',
                'assist',
                'special',
                'points',
            ])),
        ]);
    }

    public function down()
    {
        $this->dropTable('stat_weapon2_result');
    }
}

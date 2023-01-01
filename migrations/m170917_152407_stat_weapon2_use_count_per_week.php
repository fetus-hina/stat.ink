<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170917_152407_stat_weapon2_use_count_per_week extends Migration
{
    public function up()
    {
        $this->execute(
            'CREATE FUNCTION {{period2_to_timestamp}} ( IN INTEGER ) ' .
            'RETURNS TIMESTAMP(0) WITH TIME ZONE ' .
            'COST 1 ' .
            'RETURNS NULL ON NULL INPUT ' .
            'IMMUTABLE ' .
            'LANGUAGE SQL ' .
            'SECURITY INVOKER ' .
            'AS ' . $this->db->quoteValue('SELECT TO_TIMESTAMP($1 * 7200)'),
        );

        $this->createTable('stat_weapon2_use_count', [
            'period' => $this->integer()->notNull(),
            'rule_id' => $this->pkRef('rule2'),
            'weapon_id' => $this->pkRef('weapon2'),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            $this->tablePrimaryKey([
                'period',
                'rule_id',
                'weapon_id',
            ]),
        ]);

        $this->createTable('stat_weapon2_use_count_per_week', [
            'isoyear' => $this->integer()->notNull(),
            'isoweek' => $this->integer()->notNull(),
            'rule_id' => $this->pkRef('rule2'),
            'weapon_id' => $this->pkRef('weapon2'),
            'battles' => $this->bigInteger()->notNull(),
            'wins' => $this->bigInteger()->notNull(),
            $this->tablePrimaryKey([
                'isoyear',
                'isoweek',
                'rule_id',
                'weapon_id',
            ]),
        ]);
    }

    public function down()
    {
        $this->dropTable('stat_weapon2_use_count_per_week');
        $this->dropTable('stat_weapon2_use_count');
        $this->execute('DROP FUNCTION {{period2_to_timestamp}} ( IN INTEGER )');
    }
}

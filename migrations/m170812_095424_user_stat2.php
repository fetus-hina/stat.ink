<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170812_095424_user_stat2 extends Migration
{
    public function up()
    {
        $this->createTable('user_stat2', [
            'user_id' => $this->pkRef('user'),
            'battles' => $this->bigInteger()->notNull()->defaultValue(0),
            'have_win_lose' => $this->bigInteger()->notNull()->defaultValue(0),
            'win_battles' => $this->bigInteger()->notNull()->defaultValue(0),
            'have_kill_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'kill' => $this->bigInteger()->notNull()->defaultValue(0),
            'death' => $this->bigInteger()->notNull()->defaultValue(0),
            'have_kill_death_time' => $this->bigInteger()->notNull()->defaultValue(0),
            'kill_with_time' => $this->bigInteger()->notNull()->defaultValue(0),
            'death_with_time' => $this->bigInteger()->notNull()->defaultValue(0),
            'total_seconds' => $this->bigInteger()->notNull()->defaultValue(0),

            'turf_battles' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_have_win_lose' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_win_battles' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_have_kill_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_kill' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_have_inked' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_total_inked' => $this->bigInteger()->notNull()->defaultValue(0),
            'turf_max_inked' => $this->bigInteger()->notNull()->defaultValue(0),

            'gachi_battles' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_have_win_lose' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_win_battles' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_have_kill_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_kill' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_death' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_kill_death_time' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_kill_with_time' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_death_with_time' => $this->bigInteger()->notNull()->defaultValue(0),
            'gachi_total_seconds' => $this->bigInteger()->notNull()->defaultValue(0),
            'area_rank_peak' => $this->integer()->notNull()->defaultValue(0),
            'yagura_rank_peak' => $this->integer()->notNull()->defaultValue(0),
            'hoko_rank_peak' => $this->integer()->notNull()->defaultValue(0),

            'updated_at' => $this->timestampTZ()->notNull(),

            'PRIMARY KEY ([[user_id]])',
        ]);
    }

    public function down()
    {
        $this->dropTable('user_stat2');
    }
}

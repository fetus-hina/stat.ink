<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160825_115642_user_stat_gachi_per_min extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user_stat}} ' . implode(', ', [
            'ADD COLUMN [[gachi_kd_battle]] BIGINT NOT NULL DEFAULT 0',
            'ADD COLUMN [[gachi_kill2]] BIGINT NOT NULL DEFAULT 0',
            'ADD COLUMN [[gachi_death2]] BIGINT NOT NULL DEFAULT 0',
            'ADD COLUMN [[gachi_total_time]] BIGINT NOT NULL DEFAULT 0',
        ]));
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user_stat}} ' . implode(', ', [
            'DROP COLUMN [[gachi_kd_battle]]',
            'DROP COLUMN [[gachi_kill2]]',
            'DROP COLUMN [[gachi_death2]]',
            'DROP COLUMN [[gachi_total_time]]',
        ]));
    }
}

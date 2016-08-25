<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160825_124759_user_stat_gachi_peak extends Migration
{
    public function up()
    {
        $this->execute('ALTER TABLE {{user_stat}} ADD COLUMN [[gachi_rank_peak]] INTEGER NOT NULL DEFAULT 0');
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user_stat}} DROP COLUMN [[gachi_rank_peak]]');
    }
}

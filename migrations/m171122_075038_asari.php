<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171122_075038_asari extends Migration
{
    public function up()
    {
        $this->insert('rule2', [
            'key' => 'asari',
            'name' => 'Clam Blitz',
            'short_name' => 'CB',
        ]);
        $this->execute(
            'ALTER TABLE {{user_stat2}} ' .
            'ADD COLUMN [[asari_rank_peak]] INTEGER NOT NULL DEFAULT 0',
        );
    }

    public function down()
    {
        $this->execute('ALTER TABLE {{user_stat2}} DROP COLUMN [[asari_rank_peak]]');
        $this->delete('rule2', ['key' => 'asari']);
    }
}

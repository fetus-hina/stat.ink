<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170329_092025_bonus2 extends Migration
{
    public function up()
    {
        $this->createTable('turfwar_win_bonus2', [
            'id' => $this->primaryKey(),
            'bonus' => $this->integer()->notNull()->check('[[bonus]] >= 0'),
            'start_at' => $this->timestampTZ()->notNull(),
        ]);
        $this->insert('turfwar_win_bonus2', [
            'bonus' => 1000,
            'start_at' => '2017-03-25 04:00:00+09:00',
        ]);
    }

    public function down()
    {
        $this->dropTable('turfwar_win_bonus2');
    }
}

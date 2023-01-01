<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170507_112359_battle_death_reason2 extends Migration
{
    public function up()
    {
        $this->createTable('battle_death_reason2', [
            'battle_id' => $this->pkRef('battle2'),
            'reason_id' => $this->pkRef('death_reason2'),
            'count' => $this->integer()->notNull(),
            'PRIMARY KEY([[battle_id]], [[reason_id]])',
        ]);
    }

    public function down()
    {
        $this->dropTable('battle_death_reason2');
    }
}

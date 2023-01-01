<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170507_093805_battle_events2 extends Migration
{
    public function up()
    {
        $this->createTable('battle_events2', [
            'id' => $this->bigPkRef('battle2'),
            'events' => $this->text()->notNull(),
        ]);
    }

    public function down()
    {
        $this->dropTable('battle_events2');
    }
}

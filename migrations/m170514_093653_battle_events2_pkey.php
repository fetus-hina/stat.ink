<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170514_093653_battle_events2_pkey extends Migration
{
    public function up()
    {
        $this->addPrimaryKey('pk_battle_events2', 'battle_events2', 'id');
    }

    public function down()
    {
        $this->dropPrimaryKey('pk_battle_events2', 'battle_events2');
    }
}

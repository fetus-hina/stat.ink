<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m171201_122753_star_rank extends Migration
{
    public function up()
    {
        $this->addColumn('battle2', 'star_rank', 'INTEGER');
        $this->addColumn('battle_player2', 'star_rank', 'INTEGER');
    }

    public function down()
    {
        $this->dropColumn('battle_player2', 'star_rank');
        $this->dropColumn('battle2', 'star_rank');
    }
}

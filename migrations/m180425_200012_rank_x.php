<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180425_200012_rank_x extends Migration
{
    public function up()
    {
        $this->addColumn(
            'battle2',
            'estimate_x_power',
            $this->integer()->null(),
        );
        $this->addColumn(
            'battle_player2',
            'top_500',
            $this->boolean()->null(),
        );
    }

    public function down()
    {
        $this->dropColumn('battle2', 'estimate_x_power');
        $this->dropColumn('battle_player2', 'top_500');
    }
}

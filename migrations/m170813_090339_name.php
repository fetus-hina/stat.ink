<?php
/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170813_090339_name extends Migration
{
    public function up()
    {
        $this->addColumn('battle_player2', 'name', 'VARCHAR(10) NULL');
    }

    public function down()
    {
        $this->dropColumn('battle_player2', 'name');
    }
}

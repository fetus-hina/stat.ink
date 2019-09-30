<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170714_104041_ikanakama2 extends Migration
{
    public function up()
    {
        $this->addColumn('user', 'ikanakama2', $this->integer());
    }

    public function down()
    {
        $this->dropColumn('user', 'ikanakama2');
    }
}

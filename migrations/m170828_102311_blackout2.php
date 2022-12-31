<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170828_102311_blackout2 extends Migration
{
    public function up()
    {
        $this->execute(
            'ALTER TABLE {{user}} ' .
            "ADD COLUMN [[blackout_list]] blackout_type DEFAULT 'not-friend'::blackout_type ",
        );
    }

    public function down()
    {
        $this->execute(
            'ALTER TABLE {{user}} DROP COLUMN [[blackout_list]]',
        );
    }
}

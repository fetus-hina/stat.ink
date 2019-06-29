<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180302_051616_quadhopper_id extends Migration
{
    public function safeUp()
    {
        $this->update('weapon2', ['splatnet' => 5040], ['key' => 'quadhopper_black']);
    }

    public function safeDown()
    {
        $this->update('weapon2', ['splatnet' => null], ['key' => 'quadhopper_black']);
    }
}

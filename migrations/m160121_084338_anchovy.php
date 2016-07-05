<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m160121_084338_anchovy extends Migration
{
    public function safeUp()
    {
        $this->update('map', ['release_at' => '2016-01-22 11:00:00+09'], ['key' => 'anchovy']);
    }

    public function safeDown()
    {
        $this->update('map', ['release_at' => null], ['key' => 'anchovy']);
    }
}

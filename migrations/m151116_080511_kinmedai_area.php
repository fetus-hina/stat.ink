<?php
/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

use yii\db\Migration;

class m151116_080511_kinmedai_area extends Migration
{
    public function safeUp()
    {
        $this->update('map', ['area' => 2443], ['key' => 'kinmedai']);
    }

    public function safeDown()
    {
        $this->update('map', ['area' => null], ['key' => 'kinmedai']);
    }
}

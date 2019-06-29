<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m180301_045820_ajifry extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['splatnet' => 18], ['key' => 'ajifry']);
    }

    public function safeDown()
    {
        $this->update('map2', ['splatnet' => 16], ['key' => 'ajifry']); // wrong
    }
}

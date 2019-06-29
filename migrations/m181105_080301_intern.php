<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */
declare(strict_types=1);

use app\components\db\Migration;

class m181105_080301_intern extends Migration
{
    public function safeUp()
    {
        $this->update('salmon_title2', ['splatnet' => null], ['key' => 'intern']);
    }

    public function safeDown()
    {
        $this->update('salmon_title2', ['splatnet' => 0], ['key' => 'intern']);
    }
}

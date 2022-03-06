<?php

/**
 * @copyright Copyright (C) 2015-2016 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use yii\db\Migration;

class m151001_100307_fix_timezone extends Migration
{
    public function safeUp()
    {
        $this->update(
            'timezone',
            ['name' => 'Hawaii Time'],
            ['identifier' => 'Pacific/Honolulu']
        );
    }

    public function safeDown()
    {
        $this->update(
            'timezone',
            ['name' => 'Hawaii'],
            ['identifier' => 'Pacific/Honolulu']
        );
    }
}

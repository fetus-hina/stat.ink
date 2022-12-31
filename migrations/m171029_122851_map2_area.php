<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;
use app\components\db\StageMigration;

class m171029_122851_map2_area extends Migration
{
    use StageMigration;

    public function safeUp()
    {
        $this->setArea($this->getList());
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            ['area' => null],
            ['key' => array_keys($this->getList())],
        );
    }

    public function getList()
    {
        return [
            'mozuku'    => 2315,
            'engawa'    => 2250,
            'bbass'     => 1947,
        ];
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m180921_183535_mutsugoro extends Migration
{
    public function safeUp()
    {
        $this->update(
            'map2',
            [
                'name' => 'Skipper Pavilion',
                'short_name' => 'Pavilion',
                'splatnet' => 22, // maybe
            ],
            ['key' => 'mutsugoro'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'map2',
            [
                'name' => 'ムツゴ楼',
                'short_name' => 'ムツゴ楼',
                'splatnet' => null,
            ],
            ['key' => 'mutsugoro'],
        );
    }
}

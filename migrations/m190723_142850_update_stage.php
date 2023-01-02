<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190723_142850_update_stage extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['area' => 2642], ['key' => 'anchovy']);
        $this->update('map2', ['area' => 2743], ['key' => 'mystery_24']);
        $this->update(
            'map2',
            [
                'area' => 2439,
                'release_at' => '2018-10-03T11:00:00+09:00',
            ],
            ['key' => 'mutsugoro'],
        );
    }

    public function safeDown()
    {
        $this->update('map2', ['area' => null, 'release_at' => null], ['key' => 'mutsugoro']);
        $this->update('map2', ['area' => null], ['key' => 'mystery_24']);
        $this->update('map2', ['area' => null], ['key' => 'anchovy']);
    }
}

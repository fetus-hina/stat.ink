<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170912_122103_map2_area extends Migration
{
    public function safeUp()
    {
        $map = [
            'gangaze'   => 2140,
            'kombu'     => 2259,
            'ama'       => 2465,
            'chozame'   => 2355,
            'hokke'     => 2455,
            'tachiuo'   => 2215,
            'manta'     => 2355,
        ];
        foreach ($map as $key => $area) {
            $this->update('map2', ['area' => $area], ['key' => $key]);
        }
    }

    public function safeDown()
    {
        $this->update('map2', ['area' => null], [
            'key' => [
                'gangaze',
                'kombu',
                'ama',
                'chozame',
                'hokke',
                'tachiuo',
                'manta',
            ],
        ]);
    }
}

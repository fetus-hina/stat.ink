<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170707_115730_map2 extends Migration
{
    public function safeUp()
    {
        $this->batchInsert('map2', ['key', 'name', 'short_name', 'area', 'release_at'], [
            [
                'chozame',
                'Sturgeon Shipyard',
                'Shipyard',
                null,
                null,
            ],
            [
                'hokke',
                'Port Mackerel',
                'Port',
                null,
                null,
            ],
            [
                'tachiuo',
                'Moray Towers',
                'Towers',
                null,
                null,
            ],
        ]);
    }

    public function safeDown()
    {
        $this->delete('map2', [
            'key' => [
                'chozame',
                'hokke',
                'tachiuo',
            ],
        ]);
    }
}

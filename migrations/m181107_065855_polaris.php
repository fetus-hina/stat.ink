<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181107_065855_polaris extends Migration
{
    public function safeUp()
    {
        $this->update(
            'salmon_map2',
            ['splatnet_hint' => '/images/coop_stage/50064ec6e97aac91e70df5fc2cfecf61ad8615fd.png'],
            ['key' => 'polaris'],
        );
    }

    public function safeDown()
    {
        $this->update(
            'salmon_map2',
            ['splatnet_hint' => null],
            ['key' => 'polaris'],
        );
    }
}

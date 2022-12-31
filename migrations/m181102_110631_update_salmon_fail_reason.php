<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181102_110631_update_salmon_fail_reason extends Migration
{
    public function safeUp()
    {
        foreach ($this->getMap() as $old => $new) {
            $this->update(
                'salmon_fail_reason2',
                ['key' => $new],
                ['key' => $old],
            );
        }
    }

    public function safeDown()
    {
        foreach ($this->getMap() as $old => $new) {
            $this->update(
                'salmon_fail_reason2',
                ['key' => $old],
                ['key' => $new],
            );
        }
    }

    public function getMap()
    {
        return [
            // old => new
            'annihilated' => 'wipe_out',
            'time_up' => 'time_limit',
        ];
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m181205_064208_s2_v4_3_abilities extends Migration
{
    public function safeUp()
    {
        $this->update('ability2', ['splatnet' => 200], ['key' => 'bomb_defense_up_dx']);
        $this->update('ability2', ['splatnet' => 201], ['key' => 'main_power_up']);
    }

    public function safeDown()
    {
        $this->update('ability2', ['splatnet' => null], ['key' => [
            'bomb_defense_up_dx',
            'main_power_up',
        ],
        ]);
    }
}

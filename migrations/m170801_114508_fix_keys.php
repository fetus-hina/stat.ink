<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170801_114508_fix_keys extends Migration
{
    public function safeUp()
    {
        $this->update('map2', ['key' => 'kombu'], ['key' => 'combu']);
        $this->update('weapon2', ['key' => 'maneuver'], ['key' => 'manueuver']);
        $this->update('weapon2', ['key' => 'maneuver_collabo'], ['key' => 'manueuver_collabo']);
        $this->update('death_reason2', ['key' => 'maneuver'], ['key' => 'manueuver']);
        $this->update('death_reason2', ['key' => 'maneuver_collabo'], ['key' => 'manueuver_collabo']);
    }

    public function safeDown()
    {
        $this->update('map2', ['key' => 'combu'], ['key' => 'kombu']);
        $this->update('weapon2', ['key' => 'manueuver'], ['key' => 'maneuver']);
        $this->update('weapon2', ['key' => 'manueuver_collabo'], ['key' => 'maneuver_collabo']);
        $this->update('death_reason2', ['key' => 'manueuver'], ['key' => 'maneuver']);
        $this->update('death_reason2', ['key' => 'manueuver_collabo'], ['key' => 'maneuver_collabo']);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170801_111420_fix_maneuver_collabo extends Migration
{
    public function safeUp()
    {
        foreach (['weapon2', 'death_reason2'] as $table) {
            $this->update(
                "{{{$table}}}",
                ['name' => 'Enperry Splat Dualies'],
                ['key' => 'manueuver_collabo'],
            );
        }
    }

    public function safeDown()
    {
        foreach (['weapon2', 'death_reason2'] as $table) {
            $this->update(
                "{{{$table}}}",
                ['name' => 'スプラマニューバーコラボ'],
                ['key' => 'manueuver_collabo'],
            );
        }
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170803_145725_fix_period extends Migration
{
    public function safeUp()
    {
        $guessBattleStart = sprintf(
            '(CASE %s END)',
            implode(' ', [
                "WHEN [[start_at]] IS NOT NULL THEN [[start_at]] - '5 seconds'::interval",
                "WHEN [[end_at]] IS NOT NULL THEN [[end_at]] - '195 seconds'::interval",
                "ELSE [[created_at]] - '210 seconds'::interval",
            ]),
        );
        $calcBattlePeriod = sprintf(
            '(EXTRACT(epoch from %s) / %d)',
            $guessBattleStart,
            2 * 3600,
        );
        $this->execute(sprintf(
            'UPDATE {{battle2}} SET [[period]] = %s WHERE [[period]] IS NULL',
            $calcBattlePeriod,
        ));
    }

    public function safeDown()
    {
        $this->update('{{battle2}}', ['period' => null]);
    }
}

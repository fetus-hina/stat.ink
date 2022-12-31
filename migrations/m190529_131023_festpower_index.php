<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m190529_131023_festpower_index extends Migration
{
    public function safeUp()
    {
        $this->execute(
            'CREATE INDEX {{battle2_festpower_diff}} ON {{battle2}} ' .
            '(ABS([[my_team_estimate_fest_power]] - [[his_team_estimate_fest_power]])) ' .
            'WHERE ((' . implode(') AND (', [
                '[[my_team_estimate_fest_power]] IS NOT NULL',
                '[[his_team_estimate_fest_power]] IS NOT NULL',
                '[[is_win]] IS NOT NULL',
                '[[period]] IS NOT NULL',
            ]) . '))',
        );
    }

    public function safeDown()
    {
        $this->execute('DROP INDEX {{battle2_festpower_diff}}');
    }
}

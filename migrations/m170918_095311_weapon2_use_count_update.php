<?php

/**
 * @copyright Copyright (C) 2015-2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

use app\components\db\Migration;

class m170918_095311_weapon2_use_count_update extends Migration
{
    public function up()
    {
        foreach (['stat_weapon2_use_count', 'stat_weapon2_use_count_per_week'] as $table) {
            $this->execute("TRUNCATE TABLE {{{$table}}}");
            $this->execute("ALTER TABLE {{{$table}}} " . implode(', ', [
                'ADD COLUMN [[kills]] BIGINT NOT NULL',
                'ADD COLUMN [[deaths]] BIGINT NOT NULL',
                'ADD COLUMN [[kd_available]] BIGINT NOT NULL',
                'ADD COLUMN [[kills_with_time]] BIGINT NOT NULL',
                'ADD COLUMN [[deaths_with_time]] BIGINT NOT NULL',
                'ADD COLUMN [[kd_time_available]] BIGINT NOT NULL',
                'ADD COLUMN [[kd_time_seconds]] BIGINT NOT NULL',
                'ADD cOLUMN [[specials]] BIGINT NOT NULL',
                'ADD COLUMN [[specials_available]] BIGINT NOT NULL',
                'ADD COLUMN [[specials_with_time]] BIGINT NOT NULL',
                'ADD COLUMN [[specials_time_available]] BIGINT NOT NULL',
                'ADD COLUMN [[specials_time_seconds]] BIGINT NOT NULL',
                'ADD COLUMN [[inked]] BIGINT NOT NULL',
                'ADD COLUMN [[inked_available]] BIGINT NOT NULL',
                'ADD COLUMN [[inked_with_time]] BIGINT NOT NULL',
                'ADD COLUMN [[inked_time_available]] BIGINT NOT NULL',
                'ADD COLUMN [[inked_time_seconds]] BIGINT NOT NULL',
                'ADD COLUMN [[knockout_wins]] BIGINT NULL',
                'ADD COLUMN [[timeup_wins]] BIGINT NULL',
                'ADD COLUMN [[knockout_loses]] BIGINT NULL',
                'ADD COLUMN [[timeup_loses]] BIGINT NULL',
            ]));
        }
    }

    public function down()
    {
        foreach (['stat_weapon2_use_count', 'stat_weapon2_use_count_per_week'] as $table) {
            $this->execute("ALTER TABLE {{{$table}}} " . implode(', ', array_map(
                fn (string $column): string => 'DROP COLUMN [[' . $column . ']]',
                [
                    'kills',
                    'deaths',
                    'kd_available',
                    'kills_with_time',
                    'deaths_with_time',
                    'kd_time_available',
                    'kd_time_seconds',
                    'specials',
                    'specials_available',
                    'specials_with_time',
                    'specials_time_available',
                    'specials_time_seconds',
                    'inked',
                    'inked_available',
                    'inked_with_time',
                    'inked_time_available',
                    'inked_time_seconds',
                    'knockout_wins',
                    'timeup_wins',
                    'knockout_loses',
                    'timeup_loses',
                ]
            )));
        }
    }
}

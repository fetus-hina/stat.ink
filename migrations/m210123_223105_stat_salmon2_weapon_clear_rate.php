<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m210123_223105_stat_salmon2_weapon_clear_rate extends Migration
{
    public function safeUp()
    {
        $this->createTable('stat_salmon2_weapon_clear_rate', [
            'stage_id' => $this->pkRef('salmon_map2')->notNull(),
            'weapon_id' => $this->pkRef('weapon2')->notNull(),
            'plays' => $this->bigInteger()->notNull(),
            'cleared' => $this->bigInteger()->notNull(),
            'last_data_at' => $this->timestampTZ()->notNull(),
            'PRIMARY KEY ([[stage_id]], [[weapon_id]])',
        ]);
        $this->execute(<<<'END_SQL'
            INSERT INTO {{stat_salmon2_weapon_clear_rate}}
            SELECT
              {{salmon2}}.[[stage_id]],
              {{salmon_weapon2}}.[[weapon_id]],
              COUNT(*) AS [[plays]],
              SUM(CASE WHEN {{salmon2}}.[[clear_waves]] = 3 THEN 1 ELSE 0 END) AS [[cleared]],
              MAX({{salmon2}}.[[updated_at]]) AS [[last_data_at]]
            FROM {{salmon2}}
            INNER JOIN {{salmon_schedule2}} ON
                TO_TIMESTAMP({{salmon2}}.[[shift_period]] * 7200) = {{salmon_schedule2}}.[[start_at]]
              AND
                {{salmon2}}.[[stage_id]] = {{salmon_schedule2}}.[[map_id]]
            INNER JOIN {{salmon_weapon2}} ON {{salmon_schedule2}}.[[id]] = {{salmon_weapon2}}.[[schedule_id]]
            WHERE {{salmon2}}.[[stage_id]] IS NOT NULL
            AND {{salmon2}}.[[shift_period]] IS NOT NULL
            AND {{salmon2}}.[[clear_waves]] BETWEEN 0 AND 3
            AND ({{salmon2}}.[[clear_waves]] = 3 OR {{salmon2}}.[[fail_reason_id]] IS NOT NULL)
            AND {{salmon2}}.[[is_automated]] = TRUE
            AND (
                (
                    {{salmon2}}.[[start_at]] IS NOT NULL
                  AND
                    {{salmon2}}.[[created_at]] - {{salmon2}}.[[start_at]] <= '10 days'::interval
                )
              OR
                (
                    {{salmon2}}.[[end_at]] IS NOT NULL
                  AND
                    {{salmon2}}.[[created_at]] - {{salmon2}}.[[end_at]] <= '10 days'::interval
                )
            )
            GROUP BY {{salmon2}}.[[stage_id]], {{salmon_weapon2}}.[[weapon_id]]
        END_SQL);
    }

    public function safeDown()
    {
        $this->dropTable('stat_salmon2_weapon_clear_rate');
    }
}

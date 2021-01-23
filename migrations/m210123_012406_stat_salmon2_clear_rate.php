<?php

/**
 * @copyright Copyright (C) 2015-2021 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\db\Migration;

class m210123_012406_stat_salmon2_clear_rate extends Migration
{
    public function safeUp()
    {
        $this->createTable('stat_salmon2_clear_rate', [
            'stage_id' => $this->pkRef('salmon_map2')->notNull(),
            'plays' => $this->bigInteger()->notNull(),
            'avg_clear_waves' => $this->decimal(5, 4)->notNull(),
            'sd_clear_waves' => $this->decimal(5, 4)->notNull(),
            'cleared' => $this->bigInteger()->notNull(),
            'fail_wave1' => $this->bigInteger()->notNull(),
            'fail_wave2' => $this->bigInteger()->notNull(),
            'fail_wave3' => $this->bigInteger()->notNull(),
            'fail_wiped' => $this->bigInteger()->notNull(),
            'fail_timed' => $this->bigInteger()->notNull(),
            'avg_golden_eggs' => $this->decimal(7, 3)->null(),
            'sd_golden_eggs' => $this->decimal(7, 3)->null(),
            'avg_power_eggs' => $this->decimal(7, 3)->null(),
            'sd_power_eggs' => $this->decimal(7, 3)->null(),
            'avg_deaths' => $this->decimal(5, 3)->null(),
            'sd_deaths' => $this->decimal(5, 3)->null(),
            'last_data_at' => $this->timestampTZ()->notNull(),
            'PRIMARY KEY ([[stage_id]])',
        ]);
        $this->execute(<<<'SQL_END'
            CREATE TEMPORARY TABLE {{tmp_salmon2_stats}} AS
            SELECT
              {{salmon_player2}}.[[work_id]] AS [[id]],
              SUM({{salmon_player2}}.[[golden_egg_delivered]]) AS [[golden_eggs]],
              SUM({{salmon_player2}}.[[power_egg_collected]]) AS [[power_eggs]],
              SUM({{salmon_player2}}.[[death]]) AS [[deaths]]
            FROM {{salmon_player2}}
            INNER JOIN {{salmon2}} ON {{salmon_player2}}.[[work_id]] = {{salmon2}}.[[id]]
            WHERE {{salmon2}}.[[stage_id]] IS NOT NULL
            AND {{salmon2}}.[[clear_waves]] BETWEEN 0 AND 3
            AND ({{salmon2}}.[[clear_waves]] = 3 OR {{salmon2}}.[[fail_reason_id]] IS NOT NULL)
            AND {{salmon2}}.[[is_automated]] = TRUE
            GROUP BY {{salmon_player2}}.[[work_id]]
            HAVING COUNT(*) = 4
        SQL_END);

        $this->execute(<<<'SQL_END'
            INSERT INTO {{stat_salmon2_clear_rate}}
            SELECT
              {{salmon2}}.[[stage_id]],
              COUNT(*) AS [[plays]],
              AVG({{salmon2}}.[[clear_waves]]) AS [[avg_clear_waves]],
              STDDEV_SAMP({{salmon2}}.[[clear_waves]]) AS [[sd_clear_waves]],
              SUM(CASE WHEN {{salmon2}}.[[clear_waves]] = 3 THEN 1 ELSE 0 END) AS [[cleared]],
              SUM(CASE WHEN {{salmon2}}.[[clear_waves]] = 0 THEN 1 ELSE 0 END) AS [[fail_wave1]],
              SUM(CASE WHEN {{salmon2}}.[[clear_waves]] = 1 THEN 1 ELSE 0 END) AS [[fail_wave2]],
              SUM(CASE WHEN {{salmon2}}.[[clear_waves]] = 2 THEN 1 ELSE 0 END) AS [[fail_wave3]],
              SUM(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] < 3 AND {{salmon2}}.[[fail_reason_id]] = 1
                    THEN 1
                  ELSE 0
                END
              ) AS [[fail_wiped]],
              SUM(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] < 3 AND {{salmon2}}.[[fail_reason_id]] = 2
                    THEN 1
                  ELSE 0
                END
              ) AS [[fail_timed]],
              AVG(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] = 3 THEN {{tmp_salmon2_stats}}.[[golden_eggs]]
                  ELSE NULL
                END
              ) AS [[avg_golden_eggs]],
              STDDEV_SAMP(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] = 3 THEN {{tmp_salmon2_stats}}.[[golden_eggs]]
                  ELSE NULL
                END
              ) AS [[sd_golden_eggs]],
              AVG(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] = 3 THEN {{tmp_salmon2_stats}}.[[power_eggs]]
                  ELSE NULL
                END
              ) AS [[avg_power_eggs]],
              STDDEV_SAMP(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] = 3 THEN {{tmp_salmon2_stats}}.[[power_eggs]]
                  ELSE NULL
                END
              ) AS [[sd_power_eggs]],
              AVG(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] = 3 THEN {{tmp_salmon2_stats}}.[[deaths]]
                  ELSE NULL
                END
              ) AS [[avg_deaths]],
              STDDEV_SAMP(
                CASE
                  WHEN {{salmon2}}.[[clear_waves]] = 3 THEN {{tmp_salmon2_stats}}.[[deaths]]
                  ELSE NULL
                END
              ) AS [[sd_deaths]],
              MAX({{salmon2}}.[[updated_at]]) AS [[last_data_at]]
            FROM {{salmon2}}
            INNER JOIN {{tmp_salmon2_stats}} ON {{salmon2}}.[[id]] = {{tmp_salmon2_stats}}.[[id]]
            WHERE {{salmon2}}.[[stage_id]] IS NOT NULL
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
            GROUP BY {{salmon2}}.[[stage_id]]
        SQL_END);
    }

    public function safeDown()
    {
        $this->dropTable('stat_salmon2_clear_rate');
    }
}

<?php

/**
 * @copyright Copyright (C) 2021-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\commands\stat;

use Yii;
use yii\db\Connection;

trait Salmon2Trait
{
    protected function updateEntireSalmon2(): void
    {
        $this->makeStatSalmon2ClearRate();
        $this->makeStatSalmon2WeaponClearRate();
    }

    private function makeStatSalmon2ClearRate(): void
    {
        Yii::$app->db->transaction(function (Connection $db): void {
            $db
                ->createCommand(<<<'SQL_END'
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
                SQL_END)
                ->execute();
            $db
                ->createCommand(<<<'SQL_END'
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
                    ON CONFLICT ON CONSTRAINT {{stat_salmon2_clear_rate_pkey}} DO UPDATE
                    SET
                      [[plays]] = {{excluded}}.[[plays]],
                      [[avg_clear_waves]] = {{excluded}}.[[avg_clear_waves]],
                      [[sd_clear_waves]] = {{excluded}}.[[sd_clear_waves]],
                      [[cleared]] = {{excluded}}.[[cleared]],
                      [[fail_wave1]] = {{excluded}}.[[fail_wave1]],
                      [[fail_wave2]] = {{excluded}}.[[fail_wave2]],
                      [[fail_wave3]] = {{excluded}}.[[fail_wave3]],
                      [[fail_wiped]] = {{excluded}}.[[fail_wiped]],
                      [[fail_timed]] = {{excluded}}.[[fail_timed]],
                      [[avg_golden_eggs]] = {{excluded}}.[[avg_golden_eggs]],
                      [[sd_golden_eggs]] = {{excluded}}.[[sd_golden_eggs]],
                      [[avg_power_eggs]] = {{excluded}}.[[avg_power_eggs]],
                      [[sd_power_eggs]] = {{excluded}}.[[sd_power_eggs]],
                      [[avg_deaths]] = {{excluded}}.[[avg_deaths]],
                      [[sd_deaths]] = {{excluded}}.[[sd_deaths]],
                      [[last_data_at]] = {{excluded}}.[[last_data_at]]
                SQL_END)
                ->execute();
        });
    }

    private function makeStatSalmon2WeaponClearRate(): void
    {
        Yii::$app->db->transaction(function (Connection $db): void {
            $db
                ->createCommand(<<<'SQL_END'
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
                    ON CONFLICT ON CONSTRAINT {{stat_salmon2_weapon_clear_rate_pkey}} DO UPDATE
                    SET
                      [[plays]] = {{excluded}}.[[plays]],
                      [[cleared]] = {{excluded}}.[[cleared]],
                      [[last_data_at]] = {{excluded}}.[[last_data_at]]
                SQL_END)
                ->execute();
        });
    }
}

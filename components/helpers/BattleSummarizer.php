<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use yii\db\Query;

class BattleSummarizer
{
    public static function getSummary(Query $oldQuery)
    {
        $db = Yii::$app->db;
        $now = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
        $cond24Hours = sprintf(
            '(({{battle}}.[[end_at]] IS NOT NULL) AND ({{battle}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now - 86400 + 1)),
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now))
        );
        $condResultPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle}}.[[is_win]] IS NOT NULL',
        ]));
        $condKDPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle}}.[[kill]] IS NOT NULL',
            '{{battle}}.[[death]] IS NOT NULL',
        ]));
        // ------------------------------------------------------------------------------
        $column_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                ])
            )
        );
        $column_wp_short = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    $cond24Hours,
                    '{{battle}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    $cond24Hours,
                ])
            )
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );
        $column_kd_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );

        $query = clone $oldQuery;
        $query->orderBy(null);
        $query->select([
            'battle_count' => 'COUNT(*)',
            'wp' => $column_wp,
            'wp_short' => $column_wp_short,
            'total_kill' => $column_total_kill,
            'total_death' => $column_total_death,
            'kd_present' => $column_kd_present,
        ]);
        return (object)$query->createCommand()->queryOne();
    }

    public static function getSummary2(Query $oldQuery)
    {
        $db = Yii::$app->db;
        $now = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
        $cond24Hours = sprintf(
            '(({{battle2}}.[[end_at]] IS NOT NULL) AND ({{battle2}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue($now->sub(new DateInterval('PT86399S'))->format(DateTime::ATOM)),
            $db->quoteValue($now->format(DateTime::ATOM))
        );
        $condResultPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[is_win]] IS NOT NULL',
        ]));
        $condKDPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[kill]] IS NOT NULL',
            '{{battle2}}.[[death]] IS NOT NULL',
        ]));
        // ------------------------------------------------------------------------------
        $column_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    '{{battle2}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                ])
            )
        );
        $column_wp_short = sprintf(
            "(%s * 100.0 / NULLIF(%s, 0))",
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    $cond24Hours,
                    '{{battle2}}.[[is_win]] = TRUE',
                ])
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    $cond24Hours,
                ])
            )
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle2}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle2}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );
        $column_kd_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );

        $query = clone $oldQuery;
        $query->orderBy(null);
        $query->select([
            'battle_count' => 'COUNT(*)',
            'wp' => $column_wp,
            'wp_short' => $column_wp_short,
            'total_kill' => $column_total_kill,
            'total_death' => $column_total_death,
            'kd_present' => $column_kd_present,
            'max_kill' => 'MAX({{battle2}}.[[kill]])',
            'max_death' => 'MAX({{battle2}}.[[death]])',
            'min_kill' => 'MIN({{battle2}}.[[kill]])',
            'min_death' => 'MIN({{battle2}}.[[death]])',
            'median_kill' => 'percentile_cont(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
            'median_death' => 'percentile_cont(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
        ]);
        return (object)$query->createCommand()->queryOne();
    }
}

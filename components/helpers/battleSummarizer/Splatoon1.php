<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\battleSummarizer;

use Yii;
use yii\db\Query;

use function gmdate;
use function implode;
use function sprintf;
use function time;

trait Splatoon1
{
    public static function getSummary(Query $oldQuery)
    {
        $db = Yii::$app->db;
        $now = $_SERVER['REQUEST_TIME'] ?? time();
        $cond24Hours = sprintf(
            '(({{battle}}.[[end_at]] IS NOT NULL) AND ({{battle}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now - 86400 + 1)),
            $db->quoteValue(gmdate('Y-m-d H:i:sO', $now)),
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
                ]),
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                ]),
            ),
        );
        $column_wp_short = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    $cond24Hours,
                    '{{battle}}.[[is_win]] = TRUE',
                ]),
            ),
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    $cond24Hours,
                ]),
            ),
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ]),
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle}}.[[death]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ]),
        );
        $column_kd_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ]),
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
}

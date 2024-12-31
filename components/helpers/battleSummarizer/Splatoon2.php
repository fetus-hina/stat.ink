<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers\battleSummarizer;

use DateInterval;
use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Yii;
use app\models\Rule2;
use yii\db\Query;

use function implode;
use function sprintf;
use function time;

trait Splatoon2
{
    public static function getSummary2(Query $oldQuery)
    {
        $db = Yii::$app->db;
        $turfWarId = Rule2::findOne(['key' => 'nawabari'])->id;
        $now = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
        $cond24Hours = sprintf(
            '(({{battle2}}.[[end_at]] IS NOT NULL) AND ({{battle2}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue($now->sub(new DateInterval('PT86399S'))->format(DateTime::ATOM)),
            $db->quoteValue($now->format(DateTime::ATOM)),
        );
        $condAfterSept2017 = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[end_at]] IS NOT NULL',
            '{{battle2}}.[[end_at]] >=' . $db->quoteValue('2017-09-01T00:00:00+00:00'),
        ]));
        $condResultPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[is_win]] IS NOT NULL',
        ]));
        $condKDPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[kill]] IS NOT NULL',
            '{{battle2}}.[[death]] IS NOT NULL',
        ]));
        $condSpecialPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[special]] IS NOT NULL',
        ]));
        $condAssistPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle2}}.[[kill_or_assist]] IS NOT NULL',
            '{{battle2}}.[[kill]] IS NOT NULL',
            '{{battle2}}.[[kill_or_assist]] - {{battle2}}.[[kill]] >= 0',
        ]));
        $condInkedPresent = sprintf('(%s)', implode(' AND ', [
            $condAfterSept2017,
            '{{battle2}}.[[is_win]] IS NOT NULL',
            '{{battle2}}.[[my_point]] IS NOT NULL',
            '{{battle2}}.[[rule_id]] IS NOT NULL',
            sprintf('((%s))', implode(') OR (', [
                sprintf('(%s)', implode(' AND ', [
                    '{{battle2}}.[[rule_id]] = ' . $db->quoteValue($turfWarId),
                    '{{battle2}}.[[is_win]] = TRUE',
                    '{{battle2}}.[[my_point]] >= 1000',
                ])),
                sprintf('(%s)', implode(' AND ', [
                    '{{battle2}}.[[rule_id]] = ' . $db->quoteValue($turfWarId),
                    '{{battle2}}.[[is_win]] = FALSE',
                    '{{battle2}}.[[my_point]] >= 0',
                ])),
                sprintf('(%s)', implode(' AND ', [
                    '{{battle2}}.[[rule_id]] <> ' . $db->quoteValue($turfWarId),
                    '{{battle2}}.[[my_point]] >= 0',
                ])),
            ])),
        ]));
        // ------------------------------------------------------------------------------
        $column_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    '{{battle2}}.[[is_win]] = TRUE',
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
                    '{{battle2}}.[[is_win]] = TRUE',
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
        $column_battles_short = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condResultPresent,
                $cond24Hours,
                '{{battle2}}.[[is_win]] IS NOT NULL',
            ]),
        );
        $column_win_short = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condResultPresent,
                $cond24Hours,
                '{{battle2}}.[[is_win]] = TRUE',
            ]),
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle2}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ]),
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle2}}.[[death]] ELSE 0 END)',
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
        $column_total_specials = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle2}}.[[special]] ELSE 0 END)',
            implode(' AND ', [
                $condSpecialPresent,
            ]),
        );
        $column_specials_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condSpecialPresent,
            ]),
        );
        $assist = sprintf(
            'CASE WHEN (%s) THEN {{battle2}}.[[kill_or_assist]] - {{battle2}}.[[kill]] ELSE NULL END',
            implode(' AND ', [
                $condAssistPresent,
            ]),
        );
        $column_total_assists = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle2}}.[[kill_or_assist]] - {{battle2}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condAssistPresent,
            ]),
        );
        $column_assists_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condAssistPresent,
            ]),
        );
        $inked = sprintf('CASE %s END', implode(' ', [
            sprintf(
                'WHEN %s AND {{battle2}}.[[is_win]] = TRUE AND {{battle2}}.[[rule_id]] = %d ' .
                    'THEN {{battle2}}.[[my_point]] - 1000',
                implode(' AND ', [$condInkedPresent]),
                $turfWarId,
            ),
            sprintf(
                'WHEN %s THEN {{battle2}}.[[my_point]]',
                implode(' AND ', [$condInkedPresent]),
            ),
            'ELSE NULL',
        ]));
        $column_total_inked = sprintf('SUM(CASE %s END)', implode(' ', [
            sprintf(
                'WHEN %s AND {{battle2}}.[[is_win]] = TRUE AND {{battle2}}.[[rule_id]] = %d ' .
                    'THEN {{battle2}}.[[my_point]] - 1000',
                implode(' AND ', [$condInkedPresent]),
                $turfWarId,
            ),
            sprintf(
                'WHEN %s THEN {{battle2}}.[[my_point]]',
                implode(' AND ', [$condInkedPresent]),
            ),
            'ELSE 0',
        ]));
        $column_inked_present = sprintf('SUM(CASE WHEN %s THEN 1 ELSE 0 END)', implode(' AND ', [$condInkedPresent]));

        $query = clone $oldQuery;
        $query->orderBy(null);
        $query->select([
            'battle_count' => 'COUNT(*)',
            'wp' => $column_wp,
            'wp_short' => $column_wp_short,
            'battle_count_short' => $column_battles_short,
            'win_short' => $column_win_short,

            'kd_present' => $column_kd_present,
            'total_kill' => $column_total_kill,
            'total_death' => $column_total_death,

            'min_kill' => 'MIN({{battle2}}.[[kill]])',
            'pct5_kill' => 'percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
            'q1_4_kill' => 'percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
            'median_kill' => 'percentile_cont(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
            'q3_4_kill' => 'percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
            'pct95_kill' => 'percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle2}}.[[kill]])',
            'max_kill' => 'MAX({{battle2}}.[[kill]])',
            'stddev_kill' => 'stddev_pop({{battle2}}.[[kill]])',

            'min_death' => 'MIN({{battle2}}.[[death]])',
            'pct5_death' => 'percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
            'q1_4_death' => 'percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
            'median_death' => 'percentile_cont(0.5) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
            'q3_4_death' => 'percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
            'pct95_death' => 'percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle2}}.[[death]])',
            'max_death' => 'MAX({{battle2}}.[[death]])',
            'stddev_death' => 'stddev_pop({{battle2}}.[[death]])',

            'special_present' => $column_specials_present,
            'total_special' => $column_total_specials,

            'min_special' => 'MIN({{battle2}}.[[special]])',
            'pct5_special' => 'percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
            'q1_4_special' => 'percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
            'median_special' => 'percentile_cont(2.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
            'q3_4_special' => 'percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
            'pct95_special' => 'percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle2}}.[[special]])',
            'max_special' => 'MAX({{battle2}}.[[special]])',
            'stddev_special' => 'stddev_pop({{battle2}}.[[special]])',

            'assist_present' => $column_assists_present,
            'total_assist' => $column_total_assists,

            'min_assist' => "MIN({$assist})",
            'pct5_assist' => "percentile_cont(0.05) WITHIN GROUP (ORDER BY {$assist})",
            'q1_4_assist' => "percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {$assist})",
            'median_assist' => "percentile_cont(2.0/4) WITHIN GROUP (ORDER BY {$assist})",
            'q3_4_assist' => "percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {$assist})",
            'pct95_assist' => "percentile_cont(0.95) WITHIN GROUP (ORDER BY {$assist})",
            'max_assist' => "MAX({$assist})",
            'stddev_assist' => "stddev_pop({$assist})",

            'inked_present' => $column_inked_present,
            'total_inked' => $column_total_inked,

            'min_inked' => "MIN({$inked})",
            'pct5_inked' => "percentile_cont(0.05) WITHIN GROUP (ORDER BY {$inked})",
            'q1_4_inked' => "percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {$inked})",
            'median_inked' => "percentile_cont(2.0/4) WITHIN GROUP (ORDER BY {$inked})",
            'q3_4_inked' => "percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {$inked})",
            'pct95_inked' => "percentile_cont(0.95) WITHIN GROUP (ORDER BY {$inked})",
            'max_inked' => "MAX({$inked})",
            'stddev_inked' => "stddev_pop({$inked})",
        ]);
        return (object)$query->createCommand()->queryOne();
    }
}

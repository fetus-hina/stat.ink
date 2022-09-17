<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
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
use app\models\Rule3;
use yii\db\Query;

trait Splatoon3
{
    public static function getSummary3(Query $oldQuery)
    {
        $db = Yii::$app->db;
        $turfWarId = Rule3::findOne(['key' => 'nawabari'])->id;
        $now = (new DateTimeImmutable())
            ->setTimeZone(new DateTimeZone(Yii::$app->timeZone))
            ->setTimestamp($_SERVER['REQUEST_TIME'] ?? time());
        $cond24Hours = sprintf(
            '(({{battle3}}.[[end_at]] IS NOT NULL) AND ({{battle3}}.[[end_at]] BETWEEN %s AND %s))',
            $db->quoteValue($now->sub(new DateInterval('PT86399S'))->format(DateTime::ATOM)),
            $db->quoteValue($now->format(DateTime::ATOM))
        );
        $condResultPresent = sprintf('(%s)', implode(' AND ', [
            '{{result3}}.[[aggregatable]] = TRUE',
        ]));
        $condKDPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle3}}.[[kill]] IS NOT NULL',
            '{{battle3}}.[[death]] IS NOT NULL',
        ]));
        $condSpecialPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle3}}.[[special]] IS NOT NULL',
        ]));
        $condAssistPresent = sprintf('(%s)', implode(' AND ', [
            '{{battle3}}.[[assist]] IS NOT NULL',
        ]));
        $condInkedPresent = sprintf('(%s)', implode(' AND ', [
            '{{result3}}.[[aggregatable]] = TRUE',
            '{{battle3}}.[[inked]] IS NOT NULL',
            '{{battle3}}.[[inked]] > 0',
        ]));
        // ------------------------------------------------------------------------------
        $column_wp = sprintf(
            '(%s * 100.0 / NULLIF(%s, 0))',
            sprintf(
                'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
                implode(' AND ', [
                    $condResultPresent,
                    '{{result3}}.[[is_win]] = TRUE',
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
                    '{{result3}}.[[is_win]] = TRUE',
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
        $column_battles_short = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condResultPresent,
                $cond24Hours,
                '{{result3}}.[[aggregatable]] = TRUE',
            ])
        );
        $column_win_short = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condResultPresent,
                $cond24Hours,
                '{{result3}}.[[is_win]] = TRUE',
            ])
        );
        $column_total_kill = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle3}}.[[kill]] ELSE 0 END)',
            implode(' AND ', [
                $condKDPresent,
            ])
        );
        $column_total_death = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle3}}.[[death]] ELSE 0 END)',
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
        $column_total_specials = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle3}}.[[special]] ELSE 0 END)',
            implode(' AND ', [
                $condSpecialPresent,
            ])
        );
        $column_specials_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condSpecialPresent,
            ])
        );
        $column_total_assists = sprintf(
            'SUM(CASE WHEN (%s) THEN {{battle3}}.[[assist]] ELSE 0 END)',
            implode(' AND ', [
                $condAssistPresent,
            ])
        );
        $column_assists_present = sprintf(
            'SUM(CASE WHEN (%s) THEN 1 ELSE 0 END)',
            implode(' AND ', [
                $condAssistPresent,
            ])
        );
        $column_total_inked = sprintf('SUM(CASE WHEN %s THEN {{battle3}}.[[inked]] ELSE 0 END)', implode(' ', [
            implode(' AND ', [$condInkedPresent]),
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

            'min_kill' => 'MIN({{battle3}}.[[kill]])',
            'pct5_kill' => 'percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle3}}.[[kill]])',
            'q1_4_kill' => 'percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[kill]])',
            'median_kill' => 'percentile_cont(0.5) WITHIN GROUP (ORDER BY {{battle3}}.[[kill]])',
            'q3_4_kill' => 'percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[kill]])',
            'pct95_kill' => 'percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle3}}.[[kill]])',
            'max_kill' => 'MAX({{battle3}}.[[kill]])',
            'stddev_kill' => 'stddev_pop({{battle3}}.[[kill]])',

            'min_death' => 'MIN({{battle3}}.[[death]])',
            'pct5_death' => 'percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle3}}.[[death]])',
            'q1_4_death' => 'percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[death]])',
            'median_death' => 'percentile_cont(0.5) WITHIN GROUP (ORDER BY {{battle3}}.[[death]])',
            'q3_4_death' => 'percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[death]])',
            'pct95_death' => 'percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle3}}.[[death]])',
            'max_death' => 'MAX({{battle3}}.[[death]])',
            'stddev_death' => 'stddev_pop({{battle3}}.[[death]])',

            'special_present' => $column_specials_present,
            'total_special' => $column_total_specials,

            'min_special' => 'MIN({{battle3}}.[[special]])',
            'pct5_special' => 'percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle3}}.[[special]])',
            'q1_4_special' => 'percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[special]])',
            'median_special' => 'percentile_cont(2.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[special]])',
            'q3_4_special' => 'percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[special]])',
            'pct95_special' => 'percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle3}}.[[special]])',
            'max_special' => 'MAX({{battle3}}.[[special]])',
            'stddev_special' => 'stddev_pop({{battle3}}.[[special]])',

            'assist_present' => $column_assists_present,
            'total_assist' => $column_total_assists,

            'min_assist' => "MIN({{battle3}}.[[assist]])",
            'pct5_assist' => "percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle3}}.[[assist]])",
            'q1_4_assist' => "percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[assist]])",
            'median_assist' => "percentile_cont(2.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[assist]])",
            'q3_4_assist' => "percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[assist]])",
            'pct95_assist' => "percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle3}}.[[assist]])",
            'max_assist' => "MAX({{battle3}}.[[assist]])",
            'stddev_assist' => "stddev_pop({{battle3}}.[[assist]])",

            'inked_present' => $column_inked_present,
            'total_inked' => $column_total_inked,

            'min_inked' => "MIN({{battle3}}.[[inked]])",
            'pct5_inked' => "percentile_cont(0.05) WITHIN GROUP (ORDER BY {{battle3}}.[[inked]])",
            'q1_4_inked' => "percentile_cont(1.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[inked]])",
            'median_inked' => "percentile_cont(2.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[inked]])",
            'q3_4_inked' => "percentile_cont(3.0/4) WITHIN GROUP (ORDER BY {{battle3}}.[[inked]])",
            'pct95_inked' => "percentile_cont(0.95) WITHIN GROUP (ORDER BY {{battle3}}.[[inked]])",
            'max_inked' => "MAX({{battle3}}.[[inked]])",
            'stddev_inked' => "stddev_pop({{battle3}}.[[inked]])",
        ]);
        return (object)$query->createCommand()->queryOne();
    }
}

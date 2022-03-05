<?php

/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;

class Salmon2Query extends ActiveQuery
{
    private $summaryCache = null;

    public function summary(): array
    {
        if (!$this->summaryCache) {
            $stats = fn (
                string $suffix, // 'golden' => 'avg_golden'
                string $attribute // '{{salmon_player2}}.[[golden_egg_delivered]]
            ): array => [
                "avail_$suffix" => "SUM(CASE WHEN $attribute IS NULL THEN 0 ELSE 1 END)",
                "total_$suffix" => "SUM($attribute)",
                "avg_$suffix" => "AVG($attribute)",
                "min_$suffix" => "MIN($attribute)",
                "pct5_$suffix" => "percentile_cont(0.05) WITHIN GROUP (ORDER BY $attribute)",
                "q1_4_$suffix" => "percentile_cont(1.0/4) WITHIN GROUP (ORDER BY $attribute)",
                "median_$suffix" => "percentile_cont(0.5) WITHIN GROUP (ORDER BY $attribute)",
                "q3_4_$suffix" => "percentile_cont(3.0/4) WITHIN GROUP (ORDER BY $attribute)",
                "pct95_$suffix" => "percentile_cont(0.95) WITHIN GROUP (ORDER BY $attribute)",
                "max_$suffix" => "MAX($attribute)",
                "stddev_$suffix" => "stddev_pop($attribute)",
            ];
            $query = Salmon2::find()
                ->leftJoin('salmon_player2', '(' . implode(' AND ', [
                    'salmon_player2.work_id = salmon2.id',
                    'salmon_player2.is_me = TRUE',
                ]) . ')')
                ->where($this->where)
                ->select(array_merge(
                    [
                        'count' => 'COUNT(*)',
                        'has_result' => 'SUM(CASE WHEN [[clear_waves]] IS NULL THEN 0 ELSE 1 END)',
                        'w3_cleared' => 'SUM(CASE WHEN [[clear_waves]] >= 3 THEN 1 ELSE 0 END)',
                        'w2_cleared' => 'SUM(CASE WHEN [[clear_waves]] >= 2 THEN 1 ELSE 0 END)',
                        'w1_cleared' => 'SUM(CASE WHEN [[clear_waves]] >= 1 THEN 1 ELSE 0 END)',
                        'avg_waves' => 'AVG([[clear_waves]])',
                    ],
                    $stats('golden', '{{salmon_player2}}.[[golden_egg_delivered]]'),
                    $stats('power', '{{salmon_player2}}.[[power_egg_collected]]'),
                    $stats('rescue', '{{salmon_player2}}.[[rescue]]'),
                    $stats('death', '{{salmon_player2}}.[[death]]')
                ));
            $this->summaryCache = $query->asArray()->one();
        }

        return $this->summaryCache;
    }

    public function getHumanReadableSummary(User $user): ?string
    {
        $summary = $this->summary();
        if (!$summary || $summary['count'] < 1 || $summary['has_result'] < 1) {
            return null;
        }

        $f = Yii::$app->formatter;
        return sprintf(
            '%s [ %s ]',
            Yii::t(
                'app-salmon2',
                '{name}\'s Salmon Log',
                ['name' => $user->name]
            ),
            Yii::t(
                'app-salmon2',
                'Jobs: {jobCount} / Clear %: {clearPct} / Golden Eggs: {avgGoldenEggs} / ' .
                'Power Eggs: {avgPowerEggs} / Deaths: {avgDeaths} / Rescues: {avgRescues}',
                [
                    'jobCount' => $f->asInteger($summary['count']),
                    'clearPct' => $f->asPercent($summary['w3_cleared'] / $summary['has_result'], 1),
                    'avgGoldenEggs' => $f->asDecimal($summary['avg_golden'], 2),
                    'avgPowerEggs' => $f->asDecimal($summary['avg_power'], 2),
                    'avgDeaths' => $f->asDecimal($summary['avg_death'], 2),
                    'avgRescues' => $f->asDecimal($summary['avg_rescue'], 2),
                ]
            )
        );
    }
}

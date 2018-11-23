<?php
/**
 * @copyright Copyright (C) 2015-2018 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

class Salmon2Query extends ActiveQuery
{
    private $summaryCache = null;

    public function summary(): array
    {
        if (!$this->summaryCache) {
            $query = Salmon2::find()
                ->leftJoin('salmon_player2', '(' . implode(' AND ', [
                    'salmon_player2.work_id = salmon2.id',
                    'salmon_player2.is_me = TRUE',
                ]) . ')')
                ->where($this->where)
                ->select([
                    'count' => 'COUNT(*)',
                    'has_result' => 'SUM(CASE WHEN [[clear_waves]] IS NULL THEN 0 ELSE 1 END)',
                    'w3_cleared' => 'SUM(CASE WHEN [[clear_waves]] >= 3 THEN 1 ELSE 0 END)',
                    'w2_cleared' => 'SUM(CASE WHEN [[clear_waves]] >= 2 THEN 1 ELSE 0 END)',
                    'w1_cleared' => 'SUM(CASE WHEN [[clear_waves]] >= 1 THEN 1 ELSE 0 END)',
                    'avg_waves' => 'AVG([[clear_waves]])',
                    'avg_golden' => 'AVG({{salmon_player2}}.[[golden_egg_delivered]])',
                    'avg_power' => 'AVG({{salmon_player2}}.[[power_egg_collected]])',
                    'avg_rescue' => 'AVG({{salmon_player2}}.[[rescue]])',
                    'avg_death' => 'AVG({{salmon_player2}}.[[death]])',
                ]);
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

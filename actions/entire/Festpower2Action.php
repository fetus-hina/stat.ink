<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\entire;

use Yii;
use yii\db\Query;
use yii\db\Transaction;
use yii\helpers\ArrayHelper;
use yii\web\ViewAction;

class Festpower2Action extends ViewAction
{
    public const MISTAKE_BEGIN = '2018-05-19T04:00:00+00:00';
    public const MISTAKE_END   = '2018-05-20T14:00:00+00:00';

    public function run()
    {
        return Yii::$app->db->transaction(
            function (): string {
                return $this->controller->render(
                    'festpower2',
                    array_merge(
                        [
                            'data' => $this->getData(),
                        ],
                        $this->getTotalCounts(),
                    ),
                );
            },
            Transaction::REPEATABLE_READ,
        );
    }

    public function getData(): array
    {
        $my = '{{battle2}}.[[my_team_estimate_fest_power]]';
        $his = '{{battle2}}.[[his_team_estimate_fest_power]]';
        $mistakeBegin = (int)floor(strtotime(static::MISTAKE_BEGIN) / 7200);
        $mistakeEnd = (int)ceil(strtotime(static::MISTAKE_END) / 7200);
        $diff = sprintf('ABS(%s - %s)', $my, $his);
        $query = (new Query())
            ->select([
                'diff' => $diff,
                'battles' => 'COUNT(*)',
                'higher_wins' => sprintf('SUM(CASE %s END)', implode(' ', [
                    "WHEN {$my} < {$his} AND {{battle2}}.[[is_win]] = FALSE THEN 1",
                    "WHEN {$my} > {$his} AND {{battle2}}.[[is_win]] = TRUE THEN 1",
                    'ELSE 0',
                ])),
                'mistake_battles' => sprintf('SUM(CASE %s END)', implode(' ', [
                    vsprintf('WHEN {{battle2}}.[[period]] BETWEEN %d AND %d THEN 1', [
                        $mistakeBegin,
                        $mistakeEnd,
                    ]),
                    'ELSE 0',
                ])),
                'mistake_higher_wins' => sprintf('SUM(CASE %s END)', implode(' ', [
                    vsprintf('WHEN NOT({{battle2}}.[[period]] BETWEEN %d AND %d) THEN 0', [
                        $mistakeBegin,
                        $mistakeEnd,
                    ]),
                    "WHEN {$my} < {$his} AND {{battle2}}.[[is_win]] = FALSE THEN 1",
                    "WHEN {$my} > {$his} AND {{battle2}}.[[is_win]] = TRUE THEN 1",
                    'ELSE 0',
                ])),
            ])
            ->from('battle2')
            ->andWhere(['and',
                ['not', [$my => null]],
                ['not', [$his => null]],
                ['not', ['{{battle2}}.[[is_win]]' => null]],
                ['not', ['{{battle2}}.[[period]]' => null]],
            ])
            ->groupBy($diff)
            ->andHaving(['>=', 'COUNT(*)', 100])
            ->orderBy([$diff => SORT_ASC]);

        $prev = -10;
        return array_filter(ArrayHelper::map(
            $query->all(),
            'diff',
            function (array $row) use (&$prev): ?array {
                if ((int)$row['diff'] !== $prev + 10) {
                    return null;
                }
                $prev = (int)$row['diff'];
                return $row;
            },
        ));
    }

    public function getTotalCounts(): array
    {
        $my = '{{battle2}}.[[my_team_estimate_fest_power]]';
        $his = '{{battle2}}.[[his_team_estimate_fest_power]]';
        $mistakeBegin = (int)floor(strtotime(static::MISTAKE_BEGIN) / 7200);
        $mistakeEnd = (int)ceil(strtotime(static::MISTAKE_END) / 7200);
        $diff = sprintf('ABS(%s - %s)', $my, $his);
        $diffMistake = sprintf('CASE %s END', implode(' ', [
            vsprintf('WHEN {{battle2}}.[[period]] BETWEEN %d AND %d THEN %s', [
                $mistakeBegin,
                $mistakeEnd,
                $diff,
            ]),
            'ELSE NULL',
        ]));
        $diffNormal = sprintf('CASE %s END', implode(' ', [
            vsprintf('WHEN NOT({{battle2}}.[[period]] BETWEEN %d AND %d) THEN %s', [
                $mistakeBegin,
                $mistakeEnd,
                $diff,
            ]),
            'ELSE NULL',
        ]));
        $query = (new Query())
            ->select([
                'totalBattles' => 'COUNT(*)',
                'totalMistakeBattles' => sprintf('SUM(CASE %s END)', implode(' ', [
                    vsprintf('WHEN {{battle2}}.[[period]] BETWEEN %d AND %d THEN 1', [
                        $mistakeBegin,
                        $mistakeEnd,
                    ]),
                    'ELSE 0',
                ])),
                'avgAll' => "AVG({$diff})",
                'medianAll' => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$diff})",
                'q1All' => "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY {$diff})",
                'q3All' => "PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY {$diff})",
                'stddevAll' => "STDDEV_SAMP({$diff})",
                'avgMistake' => "AVG({$diffMistake})",
                'medianMistake' => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$diffMistake})",
                'q1Mistake' => "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY {$diffMistake})",
                'q3Mistake' => "PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY {$diffMistake})",
                'stddevMistake' => "STDDEV_SAMP({$diffMistake})",
                'avgNormal' => "AVG({$diffNormal})",
                'medianNormal' => "PERCENTILE_CONT(0.5) WITHIN GROUP (ORDER BY {$diffNormal})",
                'q1Normal' => "PERCENTILE_CONT(0.25) WITHIN GROUP (ORDER BY {$diffNormal})",
                'q3Normal' => "PERCENTILE_CONT(0.75) WITHIN GROUP (ORDER BY {$diffNormal})",
                'stddevNormal' => "STDDEV_SAMP({$diffNormal})",
            ])
            ->from('battle2')
            ->andWhere(['and',
                ['not', [$my => null]],
                ['not', [$his => null]],
                ['not', ['{{battle2}}.[[is_win]]' => null]],
                ['not', ['{{battle2}}.[[period]]' => null]],
            ]);
        return $query->one();
    }
}

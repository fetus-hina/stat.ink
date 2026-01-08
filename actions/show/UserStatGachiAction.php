<?php

/**
 * @copyright Copyright (C) 2015-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\show;

use Yii;
use app\models\Battle;
use app\models\Map;
use app\models\User;
use stdClass;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_filter;
use function array_reverse;
use function array_shift;
use function array_slice;
use function array_sum;
use function count;
use function floor;
use function round;
use function uasort;

use const SORT_DESC;

class UserStatGachiAction extends BaseAction
{
    private $user;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $this->user = User::findOne([
            'screen_name' => $request->get('screen_name'),
        ]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        return $this->controller->render('user-stat-gachi', [
            'user' => $this->user,
            'userRankStat' => $this->userRankStat,
            'recentRank' => $this->recentRankData,
            'recentWP' => $this->recentWPData,
            'maps' => $this->maps,
        ]);
    }

    public function getRecentRankData()
    {
        $query = Battle::find()
            ->with(['rankAfter', 'rule', 'lobby']) // eager loading
            ->innerJoinWith([
                'rule',
                'rule.mode',
                'rankAfter' => function (Query $q): void {
                    $q->from('rank rank_after');
                },
            ])
            ->joinWith([
                'lobby',
                'rank' => function (Query $q): void {
                    $q->from('rank rank_before');
                },
            ])
            ->andWhere([
                '{{battle}}.[[user_id]]' => $this->user->id,
                '{{game_mode}}.[[key]]' => 'gachi',
            ])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['{{lobby}}.[[key]]' => 'standard'],
                ['and',
                    ['{{lobby}}.[[key]]' => ['squad_2', 'squad_3', 'squad_4']],
                    ['or',
                        ['{{battle}}.[[rank_id]]' => null],
                        ['not', ['{{rank_before}}.[[key]]' => ['s', 's+']]],
                    ],
                ],
            ])
            ->orderBy([
                '{{battle}}.[[id]]' => SORT_DESC,
            ]);

        $index = 0;
        $ret = [];
        foreach ($query->asArray()->each(200) as $model) {
            $ret[] = (object)[
                'index' => $index--,
                'rule' => $model['rule']['key'],
                'exp' => $this->calcGraphExp(
                    $model['rankAfter']['key'],
                    $model['rank_exp_after'],
                ),
                'movingAvg' => null,
                'movingAvg50' => null,
            ];
        }
        $ret = array_reverse($ret);

        // 移動平均の計算
        $moving = [];
        $moving50 = [];
        foreach ($ret as $data) {
            $moving[] = $data->exp;
            $moving50[] = $data->exp;
            while (count($moving) > 10) {
                array_shift($moving);
            }
            if (count($moving) === 10) {
                $data->movingAvg = array_sum($moving) / 10;
            }
            while (count($moving50) > 50) {
                array_shift($moving50);
            }
            if (count($moving50) === 50) {
                $data->movingAvg50 = array_sum($moving50) / 50;
            }
        }

        return $ret;
    }

    public function getRecentWPData(): array
    {
        $query = Battle::find()
            ->with(['rule', 'map', 'lobby'])
            ->innerJoinWith(['rule', 'rule.mode'])
            ->joinWith(['lobby'])
            ->andWhere(['and',
                ['{{battle}}.[[user_id]]' => $this->user->id],
                ['{{game_mode}}.[[key]]' => 'gachi'],
                ['not', ['{{battle}}.[[is_win]]' => null]],
                ['or',
                    ['{{battle}}.[[lobby_id]]' => null],
                    ['<>', '{{lobby}}.[[key]]', 'private'],
                ],
            ])
            ->orderBy(['{{battle}}.[[id]]' => SORT_DESC]);

        $battles = [];
        foreach ($query->asArray()->each(200) as $battle) {
            $battles[] = (object)[
                'index' => -1 * count($battles),
                'is_win' => $battle['is_win'],
                'rule' => $battle['rule']['key'],
                'map' => $battle['map']['key'] ?? null,
                'totalWP' => null,
                'movingWP' => null,
                'movingWP50' => null,
            ];
        }
        if (!$battles) {
            return [];
        }

        $battles = array_reverse($battles);
        $fMoving = function (int $range, int $currentIndex) use (&$battles): ?float {
            if ($currentIndex + 1 < $range) {
                return null;
            }

            $tmp = array_slice($battles, $currentIndex + 1 - $range, $range);
            $win = count(array_filter($tmp, fn (stdClass $a): bool => (bool)$a->is_win));
            return $win * 100 / $range;
        };
        $totalWin = 0;
        $totalCount = 0;
        foreach ($battles as $i => $battle) {
            ++$totalCount;
            if ($battle->is_win) {
                ++$totalWin;
            }
            $battle->totalWP = $totalWin * 100 / $totalCount;
            $battle->movingWP = $fMoving(20, $i);
            $battle->movingWP50 = $fMoving(50, $i);
        }

        return $battles;
    }

    public function getUserRankStat()
    {
        $subQuery = (new Query())
            ->select(['id' => 'MAX({{battle}}.[[id]])'])
            ->from('battle')
            ->andWhere(['and',
                ['not', ['{{battle}}.[[rank_after_id]]' => null]],
                ['not', ['{{battle}}.[[rank_exp_after]]' => null]],
                ['{{battle}}.[[user_id]]' => $this->user->id],
            ]);

        if (!$battle = Battle::findOne(['id' => $subQuery])) {
            return null;
        }

        $deviation = null;
        $avgRank = null;
        $avgRankExp = null;
        if ($entire = $this->getEntireRankStat()) {
            $ranks = ['C-', 'C', 'C+', 'B-', 'B', 'B+', 'A-', 'A', 'A+', 'S', 'S+'];
            $avgExp = (int)round($entire->average);
            $avgRank = Yii::t('app-rank', $ranks[floor($avgExp / 100)]);
            $avgRankExp = $avgExp % 100;
        }

        return (object)[
            'rank' => Yii::t('app-rank', $battle->rankAfter->name),
            'rankExp' => (int)$battle->rank_exp_after,
            'deviation' => $deviation,
            'avgRank' => $avgRank,
            'avgRankExp' => $avgRankExp,
        ];
    }

    private function calcGraphExp($key, $exp)
    {
        $exp = (int)$exp;
        switch ($key) {
            case 's+':
                $exp += 1000;
                break;
            case 's':
                $exp += 900;
                break;
            case 'a+':
                $exp += 800;
                break;
            case 'a':
                $exp += 700;
                break;
            case 'a-':
                $exp += 600;
                break;
            case 'b+':
                $exp += 500;
                break;
            case 'b':
                $exp += 400;
                break;
            case 'b-':
                $exp += 300;
                break;
            case 'c+':
                $exp += 200;
                break;
            case 'c':
                $exp += 100;
                break;
            case 'c-':
                $exp += 0;
                break;
        }
        return $exp;
    }

    private function getEntireRankStat(): stdClass
    {
        $subQuery = (new Query())
            ->select(['id' => 'MAX({{battle}}.[[id]])'])
            ->from('battle')
            ->andWhere(['and',
                ['not', ['{{battle}}.[[rank_after_id]]' => null]],
                ['not', ['{{battle}}.[[rank_exp_after]]' => null]],
            ])
            ->groupBy('{{battle}}.[[user_id]]');

        $query = (new Query())
            ->select([
                'rank_key' => '{{rank}}.[[key]]',
                'rank_exp' => '{{battle}}.[[rank_exp_after]]',
            ])
            ->from('battle')
            ->innerJoin('rank', '{{battle}}.[[rank_after_id]] = {{rank}}.[[id]]')
            ->andWhere(['{{battle}}.[[id]]' => $subQuery]);

        $exps = [];
        foreach ($query->createCommand()->query() as $row) {
            $exps[] = $this->calcGraphExp($row['rank_key'], $row['rank_exp']);
        }
        if (count($exps) < 1) {
            return false;
        }

        $avgExp = array_sum($exps) / count($exps);

        return (object)[
            'average' => $avgExp,
        ];
    }

    public function getMaps(): array
    {
        $list = ArrayHelper::map(
            Map::find()->all(),
            'key',
            fn (Map $map): string => Yii::t('app-map', $map->name),
        );
        uasort($list, 'strnatcasecmp');
        return $list;
    }
}

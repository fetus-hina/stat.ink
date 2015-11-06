<?php
/**
 * @copyright Copyright (C) 2015 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\actions\show;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;
use app\models\Battle;
use app\models\User;

class UserStatGachiAction extends BaseAction
{
    private $user;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $this->user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        return $this->controller->render('user-stat-gachi.tpl', [
            'user' => $this->user,
            'recentRank' => $this->recentRankData,
            'recentWP' => $this->recentWPData,
        ]);
    }

    public function getRecentRankData()
    {
        $query = Battle::find()
            ->with(['rankAfter']) // eager loading
            ->innerJoinWith(['rule', 'rule.mode',
                'rankAfter' => function ($q) {
                    return $q->from('rank rank_after');
                }])
            ->joinWith(['lobby', 'rank' => function ($q) {
                    return $q->from('rank rank_before');
            }])
            ->andWhere([
                '{{battle}}.[[user_id]]' => $this->user->id,
                '{{game_mode}}.[[key]]' => 'gachi',
            ])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['{{lobby}}.[[key]]' => 'standard' ],
                ['and',
                    ['in', '{{lobby}}.[[key]]', [ 'squad_2', 'squad_3', 'squad_4' ]],
                    ['or',
                        ['{{battle}}.[[rank_id]]' => null],
                        ['not in', '{{rank_before}}.[[key]]', [ 's', 's+' ]],
                    ],
                ],
            ])
            ->orderBy('{{battle}}.[[id]] DESC');

        $index = 0;
        $ret = [];
        foreach ($query->each() as $model) {
            $ret[] = (object)[
                'index'         => $index--,
                'rule'          => $model->rule->key,
                'exp'           => $this->calcGraphExp($model->rankAfter->key, $model->rank_exp_after),
                'movingAvg'     => null,
                'movingAvg50'   => null,
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

    public function getRecentWPData()
    {
        $battles = Battle::find()
            ->innerJoinWith(['rule', 'rule.mode'])
            ->joinWith(['lobby'])
            ->andWhere([
                '{{battle}}.[[user_id]]' => $this->user->id,
                '{{game_mode}}.[[key]]' => 'gachi',
            ])
            ->andWhere(['not', ['{{battle}}.[[is_win]]' => null]])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(200)
            ->all();
        if (empty($battles)) {
            return [];
        }

        // 取得してきた中で最も古いバトルID
        $oldestId = min(
            array_map(
                function ($model) {
                    return $model->id;
                },
                $battles
            )
        );

        // 過去の情報を取得
        $query = Battle::find()
            ->innerJoinWith(['rule', 'rule.mode'])
            ->joinWith(['lobby'])
            ->andWhere([
                '{{battle}}.[[user_id]]' => $this->user->id,
                '{{game_mode}}.[[key]]' => 'gachi',
            ])
            ->andWhere(['not', ['{{battle}}.[[is_win]]' => null]])
            ->andWhere(['<', '{{battle}}.[[id]]', $oldestId])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->select([
                'win' => 'SUM(CASE WHEN {{battle}}.[[is_win]] = TRUE THEN 1 ELSE 0 END)',
                'lose' => 'SUM(CASE WHEN {{battle}}.[[is_win]] = FALSE THEN 1 ELSE 0 END)',
            ])
            ->orderBy(null);
        $total = (object)$query->createCommand()->queryOne();

        $index = -1 * (count($battles) - 1);
        $moving = [];
        $ret = array_map(
            function ($model) use (&$index, $total, &$moving) {
                if ($model->is_win) {
                    $total->win++;
                } else {
                    $total->lose++;
                }
                $totalWP = $total->win * 100 / ($total->win + $total->lose);
                $movingWP = null;
                $moving[] = $model->is_win;
                while (count($moving) > 20) {
                    array_shift($moving);
                }
                if (count($moving) === 20) {
                    $movingWin = count(array_filter($moving, function ($v) {
                        return $v === true;
                    }));
                    $movingWP = $movingWin * 100 / 20;
                }
                return (object)[
                    'index' => $index++,
                    'totalWP' => $totalWP,
                    'movingWP' => $movingWP,
                ];
            },
            array_reverse($battles)
        );

        return $ret;
    }

    private function calcGraphExp($key, $exp)
    {
        $exp = (int)$exp;
        switch ($key) {
            case 's+':
                $exp += 1000;
                break;
            case 's':
                $exp +=  900;
                break;
            case 'a+':
                $exp +=  800;
                break;
            case 'a':
                $exp +=  700;
                break;
            case 'a-':
                $exp +=  600;
                break;
            case 'b+':
                $exp +=  500;
                break;
            case 'b':
                $exp +=  400;
                break;
            case 'b-':
                $exp +=  300;
                break;
            case 'c+':
                $exp +=  200;
                break;
            case 'c':
                $exp +=  100;
                break;
            case 'c-':
                $exp +=    0;
                break;
        }
        return $exp;
    }
}

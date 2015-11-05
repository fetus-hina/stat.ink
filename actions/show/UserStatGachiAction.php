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
            ->orderBy('{{battle}}.[[id]] DESC')
            ->limit(200);

        $index = 0;
        $ret = array_reverse(
            array_map(
                function ($model) use (&$index) {
                    return (object)[
                        'index'     => $index--,
                        'battle'    => $model->id,
                        'exp'       => $this->calcGraphExp($model->rankAfter->key, $model->rank_exp_after),
                        'movingAvg' => null,
                    ];
                },
                $query->all()
            )
        );

        // 移動平均の計算
        $moving = [];
        foreach ($ret as $data) {
            $moving[] = $data->exp;
            while (count($moving) > 10) {
                array_shift($moving);
            }
            if (count($moving) === 10) {
                $data->movingAvg = array_sum($moving) / 10;
            }
        }

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

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
use app\models\Map;
use app\models\User;

class UserStatNawabariAction extends BaseAction
{
    private $user;

    public function run()
    {
        $request = Yii::$app->getRequest();
        $this->user = User::findOne(['screen_name' => $request->get('screen_name')]);
        if (!$this->user) {
            throw new NotFoundHttpException(Yii::t('app', 'Could not find user'));
        }

        return $this->controller->render('user-stat-nawabari.tpl', [
            'user' => $this->user,
            'inked' => $this->inkedData,
            'wp' => $this->wpData,
        ]);
    }

    public function getInkedData()
    {
        $query = Battle::find()
            ->with(['rule', 'map', 'bonus'])
            ->innerJoinWith(['rule', 'map', 'bonus'])
            ->joinWith(['lobby'])
            ->andWhere([
                '{{battle}}.[[user_id]]' => $this->user->id,
                '{{rule}}.[[key]]' => 'nawabari',
            ])
            ->andWhere(['not', ['{{battle}}.[[is_win]]' => null]])
            ->andWhere(['not', ['{{battle}}.[[my_point]]' => null]])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->orderBy('{{battle}}.[[id]] DESC');

        $maps = [];
        foreach (Map::find()->asArray()->all() as $map) {
            $maps[$map['key']] = (object)[
                'key' => $map['key'],
                'name' => Yii::t('app-map', $map['name']),
                'area' => $map['area'],
                'battles' => [],
                'avgInked' => null,
            ];
        }

        foreach ($query->asArray()->each(200) as $battle) {
            $tmp = $maps[$battle['map']['key']];
            $tmp->battles[] = (object)[
                'index' => -1 * count($tmp->battles),
                'inked' => max(0, $battle['my_point'] - ($battle['is_win'] ? $battle['bonus']['bonus'] : 0)),
            ];
        }

        foreach ($maps as $map) {
            if (!empty($map->battles)) {
                $sum = array_sum(
                    array_map(
                        function ($a) {
                            return $a->inked;
                        },
                        $map->battles
                    )
                );
                $map->avgInked = $sum / count($map->battles);
            }
        }

        uasort($maps, function ($a, $b) {
            return strnatcasecmp($a->name, $b->name);
        });

        return $maps;
    }

    public function getWPData()
    {
        $query = Battle::find()
            ->with(['rule'])
            ->innerJoinWith(['rule'])
            ->joinWith(['lobby'])
            ->andWhere([
                '{{battle}}.[[user_id]]' => $this->user->id,
                '{{rule}}.[[key]]' => 'nawabari',
            ])
            ->andWhere(['not', ['{{battle}}.[[is_win]]' => null]])
            ->andWhere(['or',
                ['{{battle}}.[[lobby_id]]' => null],
                ['<>', '{{lobby}}.[[key]]', 'private'],
            ])
            ->orderBy('{{battle}}.[[id]] DESC');

        $battles = [];
        foreach ($query->asArray()->each(200) as $battle) {
            $battles[] = (object)[
                'index' => -1 * count($battles),
                'is_win' => $battle['is_win'],
                'rule' => $battle['rule']['key'],
                'totalWP' => null,
                'movingWP' => null,
                'movingWP50' => null,
            ];
        }
        if (empty($battles)) {
            return [];
        }

        $battles = array_reverse($battles);
        $fMoving = function ($range, $currentIndex) use (&$battles) {
            if ($currentIndex + 1 < $range) {
                return null;
            }

            $tmp = array_slice($battles, $currentIndex + 1 - $range, $range);
            $win = count(array_filter($tmp, function ($a) {
                return $a->is_win;
            }));
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
}

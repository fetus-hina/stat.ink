<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
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
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\web\ViewAction as BaseAction;

use function array_filter;
use function array_reverse;
use function array_slice;
use function array_sum;
use function count;
use function implode;
use function max;
use function sprintf;
use function strnatcasecmp;
use function uasort;

class UserStatNawabariAction extends BaseAction
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

        return $this->controller->render('user-stat-nawabari', [
            'user' => $this->user,
            'inked' => $this->inkedData,
            'wp' => $this->wpData,
        ]);
    }

    public function getInkedData(): array
    {
        $inked = sprintf('(CASE %s END)', implode(' ', [
            'WHEN battle.is_win THEN (battle.my_point - turfwar_win_bonus.bonus)',
            'ELSE battle.my_point',
        ]));
        $stats = Battle::find()
            ->innerJoinWith(['rule', 'bonus', 'map'])
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
            ->groupBy(['{{battle}}.[[map_id]]'])
            ->orderBy(false)
            ->select([
                'map' => 'MAX({{map}}.[[key]])',
                'pct5' => "PERCENTILE_CONT(0.05) WITHIN GROUP (ORDER BY {$inked})",
                'pct50' => "PERCENTILE_CONT(0.50) WITHIN GROUP (ORDER BY {$inked})",
                'pct95' => "PERCENTILE_CONT(0.95) WITHIN GROUP (ORDER BY {$inked})",
                'avg' => "AVG({$inked})",
            ])
            ->asArray()
            ->createCommand()
            ->queryAll();
        $stats = ArrayHelper::map($stats, 'map', function (array $row): array {
            $result = [];
            foreach ($row as $k => $v) {
                if ($k !== 'map') {
                    $result[$k] = $v === null ? null : (float)$v;
                }
            }
            return $result;
        });

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
                'stats' => $stats[$map['key']] ?? [],
                'avgInked' => $stats[$map['key']]['avg'] ?? null,
            ];
        }

        foreach ($query->asArray()->each(200) as $battle) {
            $tmp = $maps[$battle['map']['key']];
            $tmp->battles[] = (object)[
                'index' => -1 * count($tmp->battles),
                'inked' => max(
                    0,
                    $battle['my_point'] - ($battle['is_win'] ? $battle['bonus']['bonus'] : 0),
                ),
            ];
        }

        foreach ($maps as $map) {
            if (!empty($map->battles)) {
                $sum = array_sum(ArrayHelper::getColumn($map->battles, 'inked', false));
                $map->avgInked = $sum / count($map->battles);
            }
        }

        uasort($maps, fn (stdClass $a, stdClass $b): int => strnatcasecmp($a->name, $b->name));

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
        $fMoving = function ($range, $currentIndex) use (&$battles): ?float {
            if ($currentIndex + 1 < $range) {
                return null;
            }

            $tmp = array_slice($battles, $currentIndex + 1 - $range, $range);
            $win = count(array_filter($tmp, fn ($a): bool => (bool)$a->is_win));
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

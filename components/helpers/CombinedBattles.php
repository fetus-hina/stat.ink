<?php
/**
 * @copyright Copyright (C) 2017 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\components\helpers;

use Yii;
use app\models\Battle2;
use app\models\Battle;
use app\models\User;
use yii\db\Query;

class CombinedBattles
{
    public static function getRecentBattles(int $num = 100) : array
    {
        return static::getRecentBattlesByQueries(
            [
                Battle::find()
                    ->with(['user', 'rule', 'map', 'battleImageResult', 'user.userIcon'])
                    ->limit($num)
                    ->orderBy(['battle.id' => SORT_DESC]),
                Battle2::find()
                    ->with(['user', 'rule', 'map', 'battleImageResult', 'user.userIcon'])
                    ->limit($num)
                    ->orderBy(['battle2.id' => SORT_DESC]),
            ],
            $num
        );
    }

    public static function getUserRecentBattles(User $user, int $num = 100) : array
    {
        return static::getRecentBattlesByQueries(
            [
                Battle::find()
                    ->andWhere(['user_id' => $user->id])
                    ->with(['user', 'rule', 'map', 'battleImageResult', 'user.userIcon'])
                    ->limit($num)
                    ->orderBy(['battle.id' => SORT_DESC]),
                Battle2::find()
                    ->andWhere(['user_id' => $user->id])
                    ->with(['user', 'rule', 'map', 'battleImageResult', 'user.userIcon'])
                    ->limit($num)
                    ->orderBy(['battle2.id' => SORT_DESC]),
            ],
            $num
        );
    }

    public static function getRecentBattlesByQueries(array $queries, int $num = 100) : array
    {
        $merged = [];
        foreach ($queries as $query) {
            $list = $query->all();
            $merged = array_merge($merged, $query->all());
        }
        $versions = [
            Battle::class  => 1,
            Battle2::class => 2,
        ];
        usort($merged, function ($a, $b) use ($versions) : int {
            $atime = $a->getCreatedAt();
            $btime = $b->getCreatedAt();
            if ($atime != $btime) {
                return $btime <=> $atime;
            }
            if (get_class($a) !== get_class($b)) {
                return $versions[get_class($b)] <=> $versions[get_class($a)];
            }
            return $b->id <=> $a->id;
        });
        return array_slice($merged, 0, $num);
    }
}

<?php

/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTime;
use DateTimeImmutable;
use Yii;
use app\models\Battle;
use app\models\Battle2;
use app\models\Salmon2;
use app\models\User;
use yii\db\ActiveQuery;

use const SORT_DESC;

class CombinedBattles
{
    public static function getRecentBattles(int $num = 100): array
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            return static::getRecentBattlesByQueries(
                [
                    [
                        'query' => Battle2::find()
                            ->with([
                                'battleImageResult',
                                'lobby',
                                'map',
                                'mode',
                                'rule',
                                'user',
                                'user.userIcon',
                            ])
                            ->limit($num)
                            ->orderBy(['battle2.id' => SORT_DESC]),
                        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
                            if (!$t) {
                                return;
                            }
                            $q->andWhere(['>=', 'battle2.created_at', $t->format(DateTime::ATOM)]);
                        },
                    ],
                    [
                        'query' => Salmon2::find()
                            ->with([
                                'stage',
                                'user',
                                'user.userIcon',
                            ])
                            ->limit($num)
                            ->orderBy(['salmon2.id' => SORT_DESC]),
                        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
                            if (!$t) {
                                return;
                            }
                            $q->andWhere(['>=', 'salmon2.created_at', $t->format(DateTime::ATOM)]);
                        },
                    ],
                    [
                        'query' => Battle::find()
                            ->with([
                                'battleImageResult',
                                'map',
                                'rule',
                                'user',
                                'user.userIcon',
                            ])
                            ->limit($num)
                            ->orderBy(['battle.id' => SORT_DESC]),
                        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
                            if (!$t) {
                                return;
                            }
                            $q->andWhere(['>=', 'battle.at', $t->format(DateTime::ATOM)]);
                        },
                    ],
                ],
                $num
            );
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function getUserRecentBattles(User $user, int $num = 100): array
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            return static::getRecentBattlesByQueries(
                [
                    [
                        'query' => Battle2::find()
                            ->andWhere(['user_id' => $user->id])
                            ->with([
                                'battleImageResult',
                                'lobby',
                                'map',
                                'mode',
                                'rule',
                                'user',
                                'user.userIcon',
                            ])
                            ->limit($num)
                            ->orderBy(['battle2.id' => SORT_DESC]),
                        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
                            if (!$t) {
                                return;
                            }
                            $q->andWhere(['>=', 'battle2.created_at', $t->format(DateTime::ATOM)]);
                        },
                    ],
                    [
                        'query' => Salmon2::find()
                            ->andWhere(['user_id' => $user->id])
                            ->with([
                                'stage',
                                'user',
                                'user.userIcon',
                            ])
                            ->limit($num)
                            ->orderBy(['salmon2.id' => SORT_DESC]),
                        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
                            if (!$t) {
                                return;
                            }
                            $q->andWhere(['>=', 'salmon2.created_at', $t->format(DateTime::ATOM)]);
                        },
                    ],
                    [
                        'query' => Battle::find()
                            ->andWhere(['user_id' => $user->id])
                            ->with([
                                'battleImageResult',
                                'map',
                                'rule',
                                'user',
                                'user.userIcon',
                            ])
                            ->limit($num)
                            ->orderBy(['battle.id' => SORT_DESC]),
                        'callback' => function (ActiveQuery $q, ?DateTimeImmutable $t): void {
                            if (!$t) {
                                return;
                            }
                            $q->andWhere(['>=', 'battle.at', $t->format(DateTime::ATOM)]);
                        },
                    ],
                ],
                $num
            );
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function getRecentBattlesByQueries(array $queries, int $num = 100): array
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            $merged = [];
            $threshold = null;
            foreach ($queries as $_) {
                Yii::beginProfile($_['query']->modelClass, __METHOD__);
                $query = clone $_['query'];
                if (isset($_['callback']) && is_callable($_['callback'])) {
                    call_user_func($_['callback'], $query, $threshold);
                }
                if ($list = $query->all()) {
                    $merged = array_merge($merged, $list);
                    usort($merged, fn ($a, $b): int => $b->getCreatedAt() <=> $a->getCreatedAt());
                    if (count($merged) >= $num) {
                        $threshold = (new DateTimeImmutable())
                            ->setTimestamp($merged[$num - 1]->getCreatedAt());
                    }
                    if (count($merged) > ceil($num * 1.2)) {
                        $merged = array_slice($merged, 0, (int)ceil($num * 1.2));
                    }
                }
                Yii::endProfile($_['query']->modelClass, __METHOD__);
            }
            $orderByClass = [
                Battle::class  => 1,
                Battle2::class => 2,
                Salmon2::class => 3,
            ];
            usort($merged, function ($a, $b) use ($orderByClass): int {
                $atime = $a->getCreatedAt();
                $btime = $b->getCreatedAt();
                return $btime <=> $atime
                    ?: $orderByClass[get_class($b)] <=> $orderByClass[get_class($a)]
                    ?: $b->id <=> $a->id;
            });
            return array_slice($merged, 0, $num);
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }
}

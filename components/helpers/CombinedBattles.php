<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\helpers;

use DateTimeImmutable;
use Yii;
use app\models\Battle;
use app\models\Battle2;
use app\models\Battle3;
use app\models\Salmon2;
use app\models\Salmon3;
use app\models\User;
use yii\base\Model;

final class CombinedBattles
{
    public static function getRecentBattles(int $num = 100): array
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            return self::getRecentBattlesByQueries(
                \array_merge(
                    require __DIR__ . '/combinedBattles/Splatoon3.php',
                    require __DIR__ . '/combinedBattles/Splatoon2.php',
                    require __DIR__ . '/combinedBattles/Splatoon1.php',
                ),
                $num,
            );
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    public static function getUserRecentBattles(User $user, int $num = 100): array
    {
        try {
            Yii::beginProfile(__FUNCTION__, __METHOD__);
            return self::getRecentBattlesByQueries(
                \array_merge(
                    require __DIR__ . '/combinedBattles/User3.php',
                    require __DIR__ . '/combinedBattles/User2.php',
                    require __DIR__ . '/combinedBattles/User1.php',
                ),
                $num,
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
                    \usort($merged, fn ($a, $b): int => self::getCreatedAt($b) <=> self::getCreatedAt($a));
                    if (\count($merged) >= $num) {
                        $threshold = (new DateTimeImmutable())
                            ->setTimestamp(self::getCreatedAt($merged[$num - 1]));
                    }
                    if (\count($merged) > \ceil($num * 1.2)) {
                        $merged = \array_slice($merged, 0, (int)\ceil($num * 1.2));
                    }
                }
                Yii::endProfile($_['query']->modelClass, __METHOD__);
            }
            $orderByClass = [
                Battle::class  => 1,
                Battle2::class => 2,
                Salmon2::class => 3,
                Battle3::class => 4,
                Salmon3::class => 5,
            ];
            \usort($merged, fn ($a, $b): int => self::getCreatedAt($b) <=> self::getCreatedAt($a)
                    ?: $orderByClass[$b::class] <=> $orderByClass[$a::class]
                    ?: $b->id <=> $a->id);
            return \array_slice(\array_values($merged), 0, $num);
        } finally {
            Yii::endProfile(__FUNCTION__, __METHOD__);
        }
    }

    /**
     * @param Battle|Battle2|Battle3|Salmon2|Salmon3|null $model
     */
    private static function getCreatedAt(?Model $model): ?int
    {
        if (
            \method_exists($model, 'getCreatedAt') &&
            \is_callable([$model, 'getCreatedAt'])
        ) {
            return self::prepareTimestamp($model->getCreatedAt());
        }

        if (isset($model->created_at)) {
            return self::prepareTimestamp($model->created_at);
        }

        if (isset($model->at)) {
            return self::prepareTimestamp($model->at);
        }

        return null;
    }

    /**
     * @param int|string|null $value
     */
    private static function prepareTimestamp($value): ?int
    {
        $intVal = \filter_var($value, FILTER_VALIDATE_INT);
        if (\is_int($intVal)) {
            return $intVal;
        }

        if (\is_string($value)) {
            $intVal = @\strtotime($value);
            if (\is_int($intVal)) {
                return $intVal;
            }
        }

        return null;
    }
}

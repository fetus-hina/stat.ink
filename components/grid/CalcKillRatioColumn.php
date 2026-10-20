<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

namespace app\components\grid;

use app\models\Battle3;
use yii\base\Model;

use function filter_var;
use function is_int;

use const FILTER_VALIDATE_INT;

/**
 * @extends KillRatioColumn<Battle3>
 */
final class CalcKillRatioColumn extends KillRatioColumn
{
    /**
     * @param Battle3 $model
     */
    protected function getKillRatio(Model $model): ?float
    {
        [$k, $d] = $this->getKD($model);
        if ($k === null || $d === null) {
            return null;
        }

        return $d < 1
            ? ($k < 1 ? 1.0 : 99.99)
            : (float)($k / $d);
    }

    /**
     * @param Battle3 $model
     */
    protected function getKillRate(Model $model): ?float
    {
        [$k, $d] = $this->getKD($model);
        if ($k === null || $d === null || $k + $d === 0) {
            return null;
        }

        return $k / ($k + $d);
    }

    /**
     * @param Battle3 $model
     * @return array{int|null, int|null}
     */
    private function getKD(Model $model): array
    {
        $k = filter_var($model->kill, FILTER_VALIDATE_INT);
        $d = filter_var($model->death, FILTER_VALIDATE_INT);

        return [
            is_int($k) && $k >= 0 ? $k : null,
            is_int($d) && $d >= 0 ? $d : null,
        ];
    }
}

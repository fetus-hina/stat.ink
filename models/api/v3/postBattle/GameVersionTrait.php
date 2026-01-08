<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

namespace app\models\api\v3\postBattle;

use app\models\SplatoonVersion3;

use const SORT_DESC;

trait GameVersionTrait
{
    protected static function gameVersion(?int $startAt): ?int
    {
        if ($startAt === null) {
            return null;
        }

        $model = SplatoonVersion3::find()
            ->andWhere(['<=', 'release_at', self::tsVal($startAt)])
            ->orderBy(['release_at' => SORT_DESC])
            ->limit(1)
            ->one();

        return $model ? (int)$model->id : null;
    }
}

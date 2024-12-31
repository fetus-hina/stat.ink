<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\actions\salmon\v3\stats\stats;

use app\models\SalmonMap3;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

use const SORT_ASC;

trait MapTrait
{
    /**
     * @return array<int, SalmonMap3>
     */
    private function getMaps(Connection $db): array
    {
        return ArrayHelper::index(
            SalmonMap3::find()->orderBy(['id' => SORT_ASC])->all($db),
            'id',
        );
    }
}

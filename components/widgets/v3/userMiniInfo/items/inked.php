<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo\items;

use Yii;
use app\models\UserStat3;

return [
    'label' => Yii::t('app', 'Avg Inked'),
    'format' => ['decimal', 0],
    'value' => fn (UserStat3 $model): ?float => $model->agg_battles
        ? $model->inked / $model->agg_battles
        : 0.0,
];

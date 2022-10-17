<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo\items;

use Yii;
use app\models\UserStat3;

return [
    'label' => Yii::t('app', 'Avg Deaths'),
    'format' => ['decimal', 2],
    'value' => fn (UserStat3 $model): ?float => $model->agg_battles
        ? $model->deaths / $model->agg_battles
        : 0.0,
];

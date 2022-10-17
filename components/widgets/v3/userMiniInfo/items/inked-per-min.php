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
    'label' => Yii::t('app', 'Inked/min'),
    'format' => ['decimal', 1],
    'value' => fn (UserStat3 $model): ?float => $model->agg_seconds
        ? $model->inked * 60.0 / $model->agg_seconds
        : 0.0,
];

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
    'label' => Yii::t('app', 'Win %'),
    'value' => fn (UserStat3 $model): string => $model->agg_battles > 0
        ? Yii::$app->formatter->asPercent($model->wins / $model->agg_battles, 1)
        : Yii::t('app', 'N/A'),
];

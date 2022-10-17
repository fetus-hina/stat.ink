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
    'label' => Yii::t('app', 'Fest Power'),
    'value' => fn (UserStat3 $model): string => $model->peak_fest_power > 0
        ? Yii::$app->formatter->asDecimal((float)$model->peak_fest_power, 1)
        : Yii::t('app', 'N/A'),
];

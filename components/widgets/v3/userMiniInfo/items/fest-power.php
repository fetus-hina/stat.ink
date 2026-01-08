<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo\items;

use Yii;
use app\components\helpers\TypeHelper;
use app\components\widgets\UserMiniInfoPowerValue;
use app\models\UserStat3;

return [
    'label' => Yii::t('app', 'Fest Power'),
    'format' => 'raw',
    'value' => fn (UserStat3 $model): string => UserMiniInfoPowerValue::widget([
        'value' => TypeHelper::floatOrNull($model->peak_fest_power),
    ]),
];

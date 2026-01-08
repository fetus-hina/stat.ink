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
    'label' => Yii::t('app', 'Kill Ratio'),
    'value' => function (UserStat3 $model): string {
        if ($model->agg_battles < 1) {
            return Yii::t('app', 'N/A');
        }

        if ($model->deaths === 0) {
            return $model->deaths === 0
                ? Yii::t('app', 'N/A')
                : Yii::$app->formatter->asDecimal(99.99, 2);
        }

        return Yii::$app->formatter->asDecimal($model->kills / $model->deaths, 2);
    },
];

<?php

/**
 * @copyright Copyright (C) 2015-2022 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

namespace app\components\widgets\v3\userMiniInfo\items;

use Yii;
use app\models\UserStat3XMatch;
use yii\helpers\Html;

return [
    'label' => Yii::t('app', 'X Power'),
    'format' => 'raw',
    'value' => function (UserStat3XMatch $model): string {
        if ($model->peak_x_power > 0) {
            $f = Yii::$app->formatter;
            $text = $f->asDecimal((float)$model->peak_x_power, 1);
            return preg_replace(
                '/[.,]\d+$/',
                Html::tag('small', '$0', ['class' => 'text-muted']),
                $text,
            );
        }

        return Yii::t('app', 'N/A');
    },
];

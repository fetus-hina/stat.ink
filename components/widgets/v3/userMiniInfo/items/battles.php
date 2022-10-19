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
use yii\helpers\Html;

return [
    'label' => Yii::t('app', 'Battles'),
    'format' => 'raw',
    'value' => function (UserStat3 $model): string {
        if (!$model->lobby) {
            return Html::encode(Yii::$app->formatter->asInteger($model->battles));
        }

        return Html::a(
            Html::encode(Yii::$app->formatter->asInteger($model->battles)),
            ['show-v3/user',
                'screen_name' => $model->user->screen_name,
                'f' => [
                    'lobby' => $model->lobby->key,
                ],
            ],
        );
    },
];

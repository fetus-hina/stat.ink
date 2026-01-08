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
use app\models\UserStat3XMatch;
use yii\helpers\Html;

return [
    'label' => Yii::t('app', 'Battles'),
    'format' => 'raw',
    'value' => function (UserStat3|UserStat3XMatch $model): string {
        if ($model instanceof UserStat3 && $model->lobby) {
            return Html::a(
                Html::encode(Yii::$app->formatter->asInteger($model->battles)),
                ['show-v3/user',
                    'screen_name' => $model->user->screen_name,
                    'f' => [
                        'lobby' => $model->lobby->key,
                    ],
                ],
            );
        }

        if ($model instanceof UserStat3XMatch && $model->rule) {
            return Html::a(
                Html::encode(Yii::$app->formatter->asInteger($model->battles)),
                ['show-v3/user',
                    'screen_name' => $model->user->screen_name,
                    'f' => [
                        'lobby' => 'xmatch',
                        'rule' => $model->rule->key,
                    ],
                ],
            );
        }

        return Html::encode(Yii::$app->formatter->asInteger($model->battles));
    },
];

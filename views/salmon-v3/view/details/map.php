<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Map3;
use app\models\Salmon3;
use app\models\SalmonMap3;
use yii\helpers\Html;

return [
  'label' => Yii::t('app', 'Stage'),
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    /** @var Map3|SalmonMap3|null */
    $stage = $model->is_big_run ? $model->bigStage : $model->stage;
    if (!$stage) {
      return null;
    }

    return implode(' ', [
      Html::a(
        Icon::search(),
        ['salmon-v3/index',
          'screen_name' => $model->user?->screen_name,
          'f' => [
            'map' => $stage->key,
          ],
        ],
      ),
      $stage instanceof SalmonMap3 ? Icon::s3SalmonStage($stage) : null,
      Html::encode(Yii::t('app-map3', $stage->name)),
    ]);
  },
];

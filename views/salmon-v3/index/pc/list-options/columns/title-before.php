<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Salmon3;

return [
  'contentOptions' => ['class' => 'cell-title'],
  'headerOptions' => ['class' => 'cell-title'],
  'label' => Yii::t('app', 'Title'),
  'value' => function (Salmon3 $model): ?string {
    if (!$model->title_before_id) {
      return null;
    }

    return implode(' ', [
      Yii::t('app-salmon-title3', $model->titleBefore->name),
      $model->title_exp_before === null
        ? ''
        : Yii::$app->formatter->asInteger($model->title_exp_before),
    ]);
  },
];

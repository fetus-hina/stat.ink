<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Turf Inked'),
  'value' => function (Battle3 $model): ?string {
    if ($model->inked === null) {
      return null;
    }

    return Yii::t('app', '{point}p', [
      'point' => Yii::$app->formatter->asInteger($model->inked),
    ]);
  },
];

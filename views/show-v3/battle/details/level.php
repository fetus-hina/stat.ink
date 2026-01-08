<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\i18n\Formatter;
use app\components\widgets\Icon;
use app\models\Battle3;

return [
  'label' => Yii::t('app', 'Level'),
  'format' => 'raw',
  'value' => function (Battle3 $model): ?string {
    if ($model->level_before === null && $model->level_after === null) {
      return null;
    }

    $f = Yii::createObject([
      'class' => Formatter::class,
      'nullDisplay' => Icon::unknown(),
    ]);

    return implode('', [
      $f->asInteger($model->level_before),
      Icon::arrowRight(),
      $f->asInteger($model->level_after),
    ]);
  },
];

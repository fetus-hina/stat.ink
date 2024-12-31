<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app', 'Special (Icon)'),
  'contentOptions' => ['class' => 'cell-special-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-special-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => Icon::s3Special($model->weapon?->special)
    ?? Icon::unknown(),
];

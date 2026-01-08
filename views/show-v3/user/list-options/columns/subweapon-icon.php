<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Battle3;

return [
  '-label' => Yii::t('app', 'Sub Weapon (Icon)'),
  'contentOptions' => ['class' => 'cell-sub-weapon-icon'],
  'format' => 'raw',
  'headerOptions' => ['class' => 'cell-sub-weapon-icon'],
  'label' => '',
  'value' => fn (Battle3 $model): string => Icon::s3Subweapon($model->weapon?->subweapon)
    ?? Icon::unknown(),
];

<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
  'attribute' => 'golden_eggs',
  'contentOptions' => ['class' => 'cell-golden-total nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-golden-total text-center'],
  'label' => Icon::goldenEgg(alt: Yii::t('app-salmon2', 'Team total Golden Eggs')),
];

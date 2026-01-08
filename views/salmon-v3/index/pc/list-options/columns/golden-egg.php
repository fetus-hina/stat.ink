<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon2', 'Golden Eggs'),
  'attribute' => 'salmonPlayer3s.0.golden_eggs',
  'contentOptions' => ['class' => 'cell-golden nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-golden text-center'],
  'label' => Icon::goldenEgg(alt: Yii::t('app-salmon2', 'Golden Eggs')),
];

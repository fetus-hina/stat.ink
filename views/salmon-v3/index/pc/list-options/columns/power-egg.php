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
  '-label' => Yii::t('app-salmon2', 'Power Eggs'),
  'attribute' => 'salmonPlayer3s.0.power_eggs',
  'contentOptions' => ['class' => 'cell-power nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-power text-center'],
  'label' => Icon::powerEgg(alt: Yii::t('app-salmon2', 'Power Eggs')),
];

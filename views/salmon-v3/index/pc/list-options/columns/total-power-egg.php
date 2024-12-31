<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon2', 'Team total Power Eggs'),
  'attribute' => 'power_eggs',
  'contentOptions' => ['class' => 'cell-power-total nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-power-total text-center'],
  'label' => Icon::powerEgg(alt: Yii::t('app-salmon2', 'Team total Power Eggs')),
];

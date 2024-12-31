<?php

/**
 * @copyright Copyright (C) 2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use yii\helpers\Html;

return [
  '-label' => Yii::t('app-salmon3', 'Bosses defeated'),
  'attribute' => 'salmonPlayer3s.0.defeat_boss',
  'contentOptions' => ['class' => 'cell-boss nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-boss text-center'],
  'label' => Icon::s3BossSalmonid('bakudan', alt: Yii::t('app-salmon3', 'Bosses defeated')),
];

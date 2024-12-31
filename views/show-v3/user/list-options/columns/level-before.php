<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  'attribute' => 'level-before',
  'contentOptions' => ['class' => 'cell-level'],
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-level'],
  'label' => Yii::t('app', 'Level'),
];

<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  'attribute' => 'start_at',
  'contentOptions' => ['class' => 'cell-reltime'],
  'format' => 'htmlRelative',
  'headerOptions' => ['class' => 'cell-reltime'],
  'label' => Yii::t('app', 'Relative Time'),
];

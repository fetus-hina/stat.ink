<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  'attribute' => 'end_at',
  'contentOptions' => ['class' => 'cell-datetime-timezone'],
  'format' => ['datetime', 'zzz'],
  'headerOptions' => ['class' => 'cell-datetime-timezone'],
  'label' => Yii::t('app', 'TZ'),
];

<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  'attribute' => 'end_at',
  'contentOptions' => ['class' => 'cell-datetime'],
  'format' => ['htmlDatetime', 'short'],
  'headerOptions' => ['class' => 'cell-datetime'],
  'label' => Yii::t('app', 'Date Time'),
];

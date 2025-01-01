<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  'attribute' => 'kill_or_assist',
  'contentOptions' => ['class' => 'cell-kill-or-assist'],
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-kill-or-assist'],
  'label' => Yii::t('app', 'Kill or Assist'),
];

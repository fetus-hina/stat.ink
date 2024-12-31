<?php

/**
 * @copyright Copyright (C) 2022-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

return [
  'label' => Yii::t('app', 'Rank in Team'),
  'attribute' => 'rank_in_team',
  'headerOptions' => ['class' => 'cell-rank-in-team'],
  'contentOptions' => ['class' => 'cell-rank-in-team'],
  'format' => 'integer',
];

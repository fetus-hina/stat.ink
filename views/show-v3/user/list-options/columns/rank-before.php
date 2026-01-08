<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Battle3;

return [
  'contentOptions' => ['class' => 'cell-rank'],
  'headerOptions' => ['class' => 'cell-rank'],
  'label' => Yii::t('app', 'Rank'),
  'value' => fn (Battle3 $model): ?string => (require __DIR__ . '/_rank.php')(
    $model->rankBefore,
    $model->rank_before_s_plus,
  ),
];

<?php

declare(strict_types=1);

use app\models\Battle3;

return [
  'contentOptions' => ['class' => 'cell-rank-after'],
  'headerOptions' => ['class' => 'cell-rank-after'],
  'label' => Yii::t('app', 'Rank (After)'),
  'value' => fn (Battle3 $model): ?string => (require __DIR__ . '/_rank.php')(
    $model->rankAfter,
    $model->rank_after_s_plus,
  ),
];

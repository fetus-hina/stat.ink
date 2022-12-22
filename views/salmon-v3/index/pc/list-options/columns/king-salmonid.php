<?php

declare(strict_types=1);

use app\components\widgets\Label;
use app\models\Salmon3;
use yii\grid\Column;

return [
  'label' => Yii::t('app-salmon3', 'King Salmonid'),
  'headerOptions' => ['class' => 'cell-king'],
  'contentOptions' => ['class' => 'cell-king'],
  'format' => 'raw',
  'value' => function (Salmon3 $model): ?string {
    if (!$king = $model->kingSalmonid) {
      return null;
    }

    $cleared = $model->clear_extra;
    if ($cleared === null) {
      return Label::widget([
        'color' => 'default',
        'content' => Yii::t('app-salmon-boss3', $king->name),
      ]);
    }

    return Label::widget([
      'color' => $cleared ? 'success' : 'danger',
      'content' => vsprintf('%s: %s', [
        Yii::t('app-salmon2', $cleared ? '✓' : '✗'),
        Yii::t('app-salmon-boss3', $king->name),
      ]),
    ]);
  },
];

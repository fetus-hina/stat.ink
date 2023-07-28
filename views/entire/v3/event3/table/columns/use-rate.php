<?php

declare(strict_types=1);

use app\components\helpers\TypeHelper;
use yii\base\Model;
use yii\bootstrap\Progress;
use yii\helpers\ArrayHelper;

/**
 * @var float $maxUseRate
 * @var int $samples
 */

return [
  'label' => Yii::t('app', 'Use %'),
  'attribute' => 'battles',
  'format' => 'raw',
  'headerOptions' => [
    'data-sort' => 'int',
    'data-sort-default' => 'desc',
  ],
  'value' => function (Model $model) use ($maxUseRate, $samples): string {
    $battles = TypeHelper::intOrNull(ArrayHelper::getValue($model, 'battles'));
    if ($battles < 1 || $samples < 1 || $maxUseRate < 0.00001) {
      return '';
    }

    $useRate = $battles / $samples;
    return Progress::widget([
      'percent' => $useRate / $maxUseRate * 100,
      'label' => Yii::$app->formatter->asPercent($useRate, 2),
      'options' => [
        'style' => [
          'min-width' => '75px',
        ],
      ],
    ]);
  },
  'contentOptions' => function (Model $model): array {
    return [
      'class' => 'text-right',
      'data' => [
        'sort-value' => TypeHelper::int(ArrayHelper::getValue($model, 'battles', -1)),
      ],
    ];
  },
];

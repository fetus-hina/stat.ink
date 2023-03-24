<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 */

return [
  'contentOptions' => function (array $row): array {
    $team = (int)ArrayHelper::getValue($row, 'defeated');
    $me = (int)ArrayHelper::getValue($row, 'defeated_by_me');
    return [
      'class' => 'text-center',
      'data-sort-value' => $team < 1 ? -1.0 : ($me / $team),
    ];
  },
  'encodeLabel' => false,
  'format' => 'raw',
  'headerOptions' => [
    'class' => 'auto-tooltip text-center',
    'data' => [
      'sort' => 'float',
      'sort-default' => 'desc',
    ],
  ],
  'label' => Yii::t('app-salmon3', 'Contribution'),
  'value' => function (array $row): string {
    $team = (int)ArrayHelper::getValue($row, 'defeated');
    $me = (int)ArrayHelper::getValue($row, 'defeated_by_me');
    return $team < 1
      ? ''
      : Html::tag(
        'span',
        Yii::$app->formatter->asPercent($me / $team, 1),
        [
          'class' => [
            'label',
            $me / $team > 0.25 ? 'label-success' : 'label-danger',
          ],
        ],
      );
  },
];

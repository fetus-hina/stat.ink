<?php

declare(strict_types=1);

use yii\helpers\ArrayHelper;
use yii\web\View;

/**
 * @var View $this
 */

$contentOptions = [
  'class' => 'text-right',
];

$headerOptions = [
  'class' => 'text-center omit',
  'style' => [
    'width' => '6em',
  ],
];

return [
  'attribute' => 'defeated',
  'contentOptions' => $contentOptions,
  'format' => 'integer',
  'headerOptions' => $headerOptions,
  'label' => Yii::t('app-salmon3', 'Defeated'),
];

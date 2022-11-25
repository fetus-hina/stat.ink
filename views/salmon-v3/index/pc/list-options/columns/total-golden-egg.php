<?php

declare(strict_types=1);

use yii\helpers\Html;
use app\assets\SalmonEggAsset;

$am = Yii::$app->assetManager;
$labelHtml = Html::img(
  $am->getAssetUrl(
    $am->getBundle(SalmonEggAsset::class),
    'golden-egg.png',
  ),
  [
    'class' => 'auto-tooltip basic-icon',
    'title' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
  ],
);

return [
  '-label' => Yii::t('app-salmon2', 'Team total Golden Eggs'),
  'attribute' => 'golden_eggs',
  'contentOptions' => ['class' => 'cell-golden-total nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-golden-total text-center'],
  'label' => $labelHtml,
];

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
    'title' => Yii::t('app-salmon2', 'Golden Eggs'),
  ],
);

return [
  '-label' => Yii::t('app-salmon2', 'Golden Eggs'),
  'attribute' => 'salmonPlayer3s.0.golden_eggs',
  'contentOptions' => ['class' => 'cell-golden nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-golden text-center'],
  'label' => $labelHtml,
];

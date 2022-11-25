<?php

declare(strict_types=1);

use yii\helpers\Html;
use app\assets\SalmonEggAsset;

$am = Yii::$app->assetManager;
$labelHtml = Html::img(
  $am->getAssetUrl(
    $am->getBundle(SalmonEggAsset::class),
    'power-egg.png',
  ),
  [
    'class' => 'auto-tooltip basic-icon',
    'title' => Yii::t('app-salmon2', 'Team total Power Eggs'),
  ],
);

return [
  '-label' => Yii::t('app-salmon2', 'Team total Power Eggs'),
  'attribute' => 'power_eggs',
  'contentOptions' => ['class' => 'cell-power-total nobr text-right'],
  'encodeLabel' => false,
  'format' => 'integer',
  'headerOptions' => ['class' => 'cell-power-total text-center'],
  'label' => $labelHtml,
];

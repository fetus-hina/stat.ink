<?php

declare(strict_types=1);

use app\assets\Spl3SalmonidAsset;
use app\components\helpers\TypeHelper;
use app\models\SalmonBoss3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var View $this
 */

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

$headerOptions = [
  'class' => 'text-center',
  'style' => [
    'width' => '30px',
  ],
];

$value = fn (array $row): string => Html::img(
  $am->getAssetUrl($am->getBundle(Spl3SalmonidAsset::class), sprintf('%s.png', rawurlencode($row['key']))),
  [
    'class' => 'auto-tooltip basic-icon text-center',
    'draggable' => 'false',
    'style' => '--icon-height:2em',
    'title' => Yii::t('app-salmon-boss3', $row['name']),
  ],
);

return [
  'format' => 'raw',
  'headerOptions' => $headerOptions,
  'label' => '',
  'value' => $value,
];

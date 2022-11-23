<?php

declare(strict_types=1);

use app\assets\SalmonEggAsset;
use app\models\Salmon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Salmon3 $model
 * @var View $this
 */

$am = Yii::$app->assetManager;
$asset = SalmonEggAsset::register($this);

echo Html::tag(
  'div',
  implode('', [
    $this->render('eggs-egg', [
      'icon' => $am->getAssetUrl($asset, 'golden-egg.png'),
      'label' => Yii::t('app-salmon2', 'Golden Eggs'),
      'value' => $model->golden_eggs,
    ]),
    $this->render('eggs-egg', [
      'icon' => $am->getAssetUrl($asset, 'power-egg.png'),
      'label' => Yii::t('app-salmon2', 'Power Eggs'),
      'value' => $model->power_eggs,
    ]),
  ]),
  [
    'class' => [
      'omit',
      'simple-battle-kill-death',
    ],
  ],
);

<?php

declare(strict_types=1);

use app\models\Map3;
use app\models\SalmonMap3;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var Map3|SalmonMap3 $map
 * @var View $this
 */

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

echo Html::tag(
  'h3',
  Html::encode(Yii::t('app-map3', $map->name)),
  [
    'class' => 'my-2',
    'id' => sprintf('event-%s', $map->key),
  ],
);

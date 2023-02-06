<?php

declare(strict_types=1);

use app\assets\Medal3Asset;
use app\models\MedalCanonical3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var MedalCanonical3 $medal
 * @var View $this
 */

$am = Yii::$app->assetManager;
assert($am instanceof AssetManager);

echo Html::tag(
  'td',
  implode(' ', [
    Html::img(
      $am->getAssetUrl(
        $am->getBundle(Medal3Asset::class),
        $medal->gold ? 'gold.png' : 'silver.png',
      ),
      [
        'class' => 'basic-icon',
        'draggable' => 'false',
      ],
    ),
    Html::encode(Yii::t('app-medal3', $medal->name)),
  ]),
  [
    'class' => 'text-left',
    'data' => [
      'sort-value' => vsprintf('%d-%s', [
        $medal->gold ? 0 : 1,
        Yii::t('app-medal3', $medal->name),
      ]),
    ],
  ],
);

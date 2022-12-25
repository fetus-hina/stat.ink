<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Rule3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var View $this
 */

echo Html::tag(
  'h2',
  vsprintf('%s %s', [
    Html::img(
      Yii::$app->assetManager->getAssetUrl(
        Yii::$app->assetManager->getBundle(GameModeIconsAsset::class),
        sprintf('spl3/%s.png', $rule->key),
      ),
      [
        'class' => 'basic-icon',
        'style' => ['--icon-height' => '1em'],
      ],
    ),
    Html::encode(Yii::t('app-rule3', $rule->name)),
  ]),
  [
    'class' => 'm-0 mb-3',
    'id' => $rule->key,
  ],
);

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

$am = Yii::$app->assetManager;

echo Html::tag(
  'h2',
  implode(' ', [
    Html::img(
      $am->getAssetUrl($am->getBundle(GameModeIconsAsset::class), sprintf('spl3/%s.png', $rule->key)),
      [
        'class' => 'basic-icon',
        'draggable' => 'false',
        'style' => ['--icon-height' => '1em'],
      ],
    ),
    Html::encode(Yii::t('app-rule3', $rule->name)),
  ]),
  ['id' => $rule->key],
);

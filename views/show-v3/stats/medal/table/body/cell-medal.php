<?php

declare(strict_types=1);

use app\models\MedalCanonical3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\AssetManager;
use yii\web\View;

/**
 * @var MedalCanonical3 $medal
 * @var View $this
 */

echo Html::tag(
  'td',
  implode(' ', [
    mb_chr($medal->gold ? 0x1F947 : 0x1F948),
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

<?php

declare(strict_types=1);

use app\assets\Spl3StageAsset;
use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3 $map
 * @var User $user
 * @var View $this
 */

echo Html::tag(
  'th',
  Html::a(
    implode('', [
      Html::tag(
        'div',
        Html::tag(
          'span',
          Html::encode(Yii::t('app-map3', $map->name)),
          [
            'class' => 'auto-tooltip',
            'title' => Yii::t('app-map3', $map->name),
          ],
        ),
        ['class' => 'omit'],
      ),
      Html::img(
        Yii::$app->assetManager->getAssetUrl(
          Spl3StageAsset::register($this),
          sprintf('color-normal/%s.jpg', $map->key),
        ),
        [
          'alt' => '',
          'class' => 'auto-tooltip h-auto w-100',
          'draggable' => 'false',
          'title' => Yii::t('app-map3', $map->name),
        ],
      ),
    ]),
    ['show-v3/user',
      'screen_name' => $user->screen_name,
      'f' => [
        'map' => $map->key,
        'result' => '~win_lose',
      ],
    ],
  ),
  [
    'class' => 'text-center',
    'scope' => 'row',
  ],
);

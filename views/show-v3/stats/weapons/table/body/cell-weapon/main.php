<?php

declare(strict_types=1);

use app\assets\Spl3WeaponAsset;
use app\models\Battle3FilterForm;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var Weapon3 $model
 */

$am = Yii::$app->assetManager;
echo Html::a(
  Html::img(
    $am->getAssetUrl(
      $am->getBundle(Spl3WeaponAsset::class),
      sprintf('main/%s.png', $model->key),
    ),
    [
      'class' => 'auto-tooltip basic-icon',
      'title' => Yii::t('app-weapon3', $model->name),
      'style' => [
        '--icon-height' => '2em',
      ],
    ],
  ),
  ['show-v3/user',
    'screen_name' => $user->screen_name,
    'f' => [
      'weapon' => $model->key,
      'result' => '~win_lose',
    ],
  ],
);

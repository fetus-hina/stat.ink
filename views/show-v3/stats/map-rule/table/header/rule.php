<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 */

echo Html::tag(
  'th',
  Html::a(
    Html::img(
      Yii::$app->assetManager->getAssetUrl(
        GameModeIconsAsset::register($this),
        sprintf('spl3/%s.png', $rule->key),
      ),
      [
        'class' => 'auto-tooltip basic-icon',
        'dragable' => 'false',
        'title' => Html::encode(Yii::t('app-rule3', $rule->name)),
      ],
    ),
    ['show-v3/user',
      'screen_name' => $user->screen_name,
      'f' => [
        'rule' => $rule->key,
        'result' => '~win_lose',
      ],
    ],
  ),
  ['class' => 'text-center'],
);

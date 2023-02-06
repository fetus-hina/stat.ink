<?php

declare(strict_types=1);

use app\assets\GameModeIconsAsset;
use app\components\widgets\Icon;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 */

$am = Yii::$app->assetManager;

echo Html::tag(
  'th',
  implode(' ', [
    Html::img(
      $am->getAssetUrl(
        $am->getBundle(GameModeIconsAsset::class),
        sprintf('spl3/%s.png', $rule->key),
      ),
      [
        'class' => 'auto-tooltip basic-icon',
        'dragable' => 'false',
        'title' => Html::encode(Yii::t('app-rule3', $rule->name)),
      ],
    ),
    Html::a(
      Icon::search(),
      ['show-v3/user',
        'screen_name' => $user->screen_name,
        'f' => [
          'rule' => $rule->key,
          'result' => '~win_lose',
        ],
      ],
    ),
  ]),
  [
    'class' => 'text-center',
    'data' => [
      'sort' => 'int',
      'sort-default' => 'desc',
    ],
  ],
);

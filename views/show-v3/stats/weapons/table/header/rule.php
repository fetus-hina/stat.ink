<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

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
    Html::encode(Yii::t('app-rule3', $rule->short_name)),
  ]),
  [
    'class' => 'text-center',
    'data' => [
      'sort' => 'int',
      'sort-default' => 'desc',
    ],
  ],
);

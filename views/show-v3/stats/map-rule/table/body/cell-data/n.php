<?php

/**
 * @copyright Copyright (C) 2023-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Map3|null $map
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 * @var int $n
 */

echo Html::tag(
  'div',
  vsprintf('%s n=%s', [
    Html::a(
      Icon::search(),
      ['show-v3/user',
        'screen_name' => $user->screen_name,
        'f' => [
          'rule' => $rule->key,
          'map' => $map?->key,
          'result' => '~win_lose',
        ],
      ]
    ),
    Html::encode(Yii::$app->formatter->asInteger($n)),
  ]),
  [
    'class' => [
      'mb-1',
      'small',
      'text-center',
      'text-muted',
    ],
  ],
);

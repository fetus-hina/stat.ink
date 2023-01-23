<?php

declare(strict_types=1);

use app\components\widgets\Icon;
use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 * @var Weapon3|null $weapon
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
          'result' => '~win_lose',
          'rule' => $rule->key,
          'weapon' => $weapon?->key,
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

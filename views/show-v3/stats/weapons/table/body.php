<?php

declare(strict_types=1);

use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, Rule3> $rules
 * @var array<string, Weapon3> $weapons
 * @var array<string, array<string, array>> $stats
 */

echo Html::tag(
  'tbody',
  implode('', [
    $this->render(
      'body/rules-weapons',
      compact('stats', 'rules', 'weapons', 'user'),
    ),
  ]),
);

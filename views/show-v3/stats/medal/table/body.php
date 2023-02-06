<?php

declare(strict_types=1);

use app\models\MedalCanonical3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, MedalCanonical3> $medals
 * @var array<string, Rule3> $rules
 * @var array<string, array<string, MedalCanonical3>> $stats
 */

echo Html::tag(
  'tbody',
  $this->render(
    'body/rules-medals',
    compact('medals', 'rules', 'stats', 'user'),
  ),
);

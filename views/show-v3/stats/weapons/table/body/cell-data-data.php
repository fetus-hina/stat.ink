<?php

declare(strict_types=1);

use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 * @var Weapon3 $weapon
 * @var array $stats
 */

echo Html::tag(
  'div',
  implode('', [
    $this->render(
      'cell-data/n',
      array_merge(
        compact('rule', 'user', 'weapon'),
        ['n' => (int)($stats['battles'] ?? 0)],
      ),
    ),
    $this->render('cell-data/kd', compact('stats')),
  ]),
  ['class' => 'mb-2'],
);

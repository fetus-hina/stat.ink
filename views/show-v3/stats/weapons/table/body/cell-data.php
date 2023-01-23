<?php

declare(strict_types=1);

use app\models\Rule3;
use app\models\User;
use app\models\Weapon3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var Rule3 $rule
 * @var User $user
 * @var View $this
 * @var Weapon3 $weapon
 * @var array|null $stats
 */

if (!$stats) {
  echo Html::tag('td', '', ['data-sort-value' => '0']);
  return;
}

echo Html::tag(
  'td',
  implode('', [
    $this->render('cell-data-chart', compact('stats')),
    $this->render(
      'cell-data-data',
      compact('rule', 'stats', 'user', 'weapon'),
    ),
  ]),
  [
    'data' => [
      'sort-value' => (int)ArrayHelper::getValue($stats, 'battles'),
    ],
  ],
);

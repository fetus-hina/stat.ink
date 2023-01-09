<?php

declare(strict_types=1);

use app\models\Rule3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, Rule3> $rules
 * @var array<string, array> $totalStats
 */

echo '<tr>';
echo '<th scope="row"></th>';
foreach ($rules as $ruleKey => $rule) {
  echo $this->render('cell-data', [
    'map' => null,
    'rule' => $rule,
    'stats' => ArrayHelper::getValue($totalStats, $ruleKey),
    'user' => $user,
  ]);
}
echo '</tr>';

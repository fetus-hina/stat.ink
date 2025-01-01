<?php

/**
 * @copyright Copyright (C) 2023-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\Map3;
use app\models\Rule3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var User $user
 * @var View $this
 * @var array<string, Map3> $maps
 * @var array<string, Rule3> $rules
 * @var array<string, array<string, array>> $mapStats
 */

foreach ($maps as $mapKey => $map) {
  echo '<tr>';
  echo $this->render('cell-map', compact('map', 'user'));
  foreach ($rules as $ruleKey => $rule) {
    echo $this->render('cell-data', [
      'map' => $map,
      'rule' => $rule,
      'stats' => ArrayHelper::getValue($mapStats, [$mapKey, $ruleKey]),
      'user' => $user,
    ]);
  }
  echo '</tr>';
}

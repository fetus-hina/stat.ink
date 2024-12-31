<?php

/**
 * @copyright Copyright (C) 2023-2024 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

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

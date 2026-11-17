<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\web\View;

/**
 * Pie chart of wins/losses with the knockout breakdown
 *
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var array<string, string> $filter
 * @var array{
 *   canKnockout: bool,
 *   battles: int,
 *   wins: int,
 *   winKnockout: int,
 *   winTime: int,
 *   winUnknown: int,
 *   loseKnockout: int,
 *   loseTime: int,
 *   loseUnknown: int
 * } $stats
 */

echo $this->render('//show-v3/stats/win-rate/pie-chart', [
  'canKnockout' => $stats['canKnockout'],
  'loseKnockout' => $stats['loseKnockout'],
  'loseTime' => $stats['loseTime'],
  'loseUnknown' => $stats['loseUnknown'],
  'winKnockout' => $stats['winKnockout'],
  'winTime' => $stats['winTime'],
  'winUnknown' => $stats['winUnknown'],
]);

echo $this->render('battles', [
  'battles' => $stats['battles'],
  'filter' => $filter,
  'form' => $form,
  'user' => $user,
]);

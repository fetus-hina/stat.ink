<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * Link to the other report (daily/monthly) of the same month
 *
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var string $label
 * @var string $route
 */

$month = $form->getFirstDayOfMonth();

?>
<p class="text-right">
  <?= Html::a(
    Html::encode($label),
    [
      $route,
      'screen_name' => $user->screen_name,
      'year' => (int)$month->format('Y'),
      'month' => (int)$month->format('n'),
    ],
    ['class' => 'btn btn-default btn-sm'],
  ) . "\n" ?>
</p>

<?php

/**
 * @copyright Copyright (C) 2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 */

declare(strict_types=1);

use app\models\Battle3FilterForm;
use app\models\Spl3YearMonthForm;
use app\models\User;
use yii\helpers\Html;
use yii\web\View;

/**
 * Number of battles, linked to the battle list of the month
 *
 * @var Spl3YearMonthForm $form
 * @var User $user
 * @var View $this
 * @var array<string, string> $filter
 * @var int $battles
 * @var string|null $align CSS class for the text alignment (default: text-center)
 */

$lastDay = $form->getFirstDayOfNextMonth()->modify('-1 day');

echo Html::tag(
  'p',
  Html::a(
    Html::encode(vsprintf('n=%s', [
      Yii::$app->formatter->asInteger($battles),
    ])),
    [
      'show-v3/user',
      'screen_name' => $user->screen_name,
      'f' => array_merge($filter, [
        'result' => Battle3FilterForm::RESULT_WIN_OR_LOSE,
        'term' => 'term',
        'term_from' => $form->getFirstDayOfMonth()->format('Y-m-d') . ' 00:00:00',
        'term_to' => $lastDay->format('Y-m-d') . ' 23:59:59',
      ]),
    ],
  ),
  ['class' => [$align ?? 'text-center', 'small', 'font-italic', 'mb-0']],
);

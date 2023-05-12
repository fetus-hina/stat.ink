<?php

declare(strict_types=1);

use app\actions\show\v3\stats\SeasonXPowerAction;
use app\models\Rule3;
use app\models\Season3;
use app\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @phpstan-import-type DailyData from SeasonXPowerAction
 *
 * @var DailyData $data
 * @var DateTimeInterface $date
 * @var Rule3 $rule
 * @var Season3 $season,
 * @var User $user
 * @var View $this
 */

$number = $data['count'] ?? 0;
if ($number < 1) {
  for ($i = 0; $i < 3; ++$i) {
    echo Html::tag('td', '-', ['class' => 'text-center text-muted']);
  }

  return;
}

$startAt = (new DateTimeImmutable())
    ->setTimezone(new DateTimeZone('Etc/UTC'))
    ->setTimestamp($date->getTimestamp())
    ->setTime(0, 0, 0);

echo Html::tag(
  'td',
  Html::a(
    Yii::$app->formatter->asInteger($number),
    ['show-v3/user',
      'screen_name' => $user->screen_name,
      'f' => [
        'lobby' => 'xmatch',
        'rule' => $rule->key,
        'term' => 'term',
        'term_from' => '@' . $startAt->getTimestamp(),
        'term_to' => '@' . ($startAt->getTimestamp() + 86400),
      ],
    ],
  ),
  ['class' => 'text-center'],
);

echo Html::tag(
  'td',
  Yii::$app->formatter->asDecimal($data['min'] ?? null, 1),
  ['class' => 'text-center'],
);

echo Html::tag(
  'td',
  Yii::$app->formatter->asDecimal($data['max'] ?? null, 1),
  ['class' => 'text-center'],
);

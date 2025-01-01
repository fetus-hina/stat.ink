<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3TideEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SalmonEvent3|'*'|null $event
 * @var SalmonWaterLevel2 $tide
 * @var StatSalmon3TideEvent[] $stats
 * @var View $this
 * @var int $tideJobs
 : @var int $totalJobs
 */

$targetStats = array_values(
  array_filter(
    $stats,
    fn (StatSalmon3TideEvent $model): bool => ($event === '*' || $model->event_id === $event?->id) &&
      $model->tide_id === $tide->id,
  ),
);

/**
 * @var StatSalmon3TideEvent|null $model
 */
$model = match (count($targetStats)) {
  0 => null,
  1 => $targetStats[0],
  default => Yii::createObject([
    'class' => StatSalmon3TideEvent::class,
    'jobs' => array_sum(ArrayHelper::getColumn($targetStats, 'jobs')),
    'cleared' => array_sum(ArrayHelper::getColumn($targetStats, 'cleared')),
  ]),
};

echo Html::tag(
  'td',
  Html::encode(
    $model && $totalJobs > 0
      ? Yii::$app->formatter->asPercent($model->jobs / $totalJobs, 2)
      : '',
  ),
  ['class' => 'text-center'],
);
echo Html::tag(
  'td',
  Html::encode(
    $event !== '*' && $model && $tideJobs > 0
      ? sprintf('(%s)', Yii::$app->formatter->asPercent($model->jobs / $tideJobs, 2))
      : '',
  ),
  ['class' => 'text-center text-muted'],
);
echo Html::tag(
  'td',
  Html::encode(
    (int)$model?->jobs > 0
      ? Yii::$app->formatter->asPercent($model->cleared / $model->jobs, 2)
      : '',
  ),
  ['class' => 'text-center'],
);

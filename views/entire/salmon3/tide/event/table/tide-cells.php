<?php

declare(strict_types=1);

use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3TideEvent;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SalmonEvent3|null $event
 * @var SalmonWaterLevel2 $tide
 * @var StatSalmon3TideEvent[] $stats
 * @var View $this
 * @var int $tideJobs
 */

$targetStats = array_values(
  array_filter(
    $stats,
    fn (StatSalmon3TideEvent $model): bool => $model->event_id === $event?->id && $model->tide_id === $tide->id,
  ),
);

/**
 * @var StatSalmon3TideEvent|null $model
 */
$model = $targetStats ? $targetStats[0] : null;

echo Html::tag(
  'td',
  Html::encode(
    $model && $tideJobs > 0
      ? Yii::$app->formatter->asPercent($model->jobs / $tideJobs, 2)
      : '',
  ),
  ['class' => 'text-right'],
);
echo Html::tag(
  'td',
  Html::encode(
    (int)$model?->jobs > 0
      ? Yii::$app->formatter->asPercent($model->cleared / $model->jobs, 2)
      : '',
  ),
  ['class' => 'text-right'],
);

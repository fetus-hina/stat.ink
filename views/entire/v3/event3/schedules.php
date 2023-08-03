<?php

declare(strict_types=1);

use app\models\Event3;
use app\models\EventSchedule3;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\View;

/**
 * @var Event3 $event
 * @var EventSchedule3 $schedule
 * @var EventSchedule3[] $schedules
 * @var View $this
 */

$placeholder = '--id--';

?>
<div class="mb-1">
  <?= Html::dropDownList(
    'event',
    (string)$schedule->id,
    ArrayHelper::map(
      $schedules,
      fn (EventSchedule3 $schedule): string => (string)$schedule->id,
      fn (EventSchedule3 $schedule): string => vsprintf('%s (%s)', [
        Yii::$app->formatter->asDate($schedule->start_at),
        Yii::t('app-rule3', $schedule->rule?->name ?? '?'),
      ]),
    ),
    [
      'class' => 'form-control mb-0',
      'data' => [
        'template' => Url::to(
          ['entire/event3', 'event' => $event->id, 'schedule' => $placeholder],
          true,
        ),
      ],
      'onchange' => vsprintf('window.location.href = this.dataset.template.replace(%s, this.value)', [
        Json::encode($placeholder),
      ]),
    ],
  ) . "\n" ?>
</div>

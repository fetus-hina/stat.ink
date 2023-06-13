<?php

declare(strict_types=1);

use app\models\SalmonEvent3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3TideEvent;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var SalmonEvent3|'*'|null $event
 * @var StatSalmon3TideEvent[] $stats
 * @var View $this
 * @var array<int, SalmonWaterLevel2> $tides
 */

$eventStats = array_values(
  $event === '*'
    ? $stats
    : array_filter(
      $stats,
      fn (StatSalmon3TideEvent $model): bool => $model->event_id === $event?->id,
    ),
);

if (!$eventStats) {
  return;
}

?>
<?= Html::tag(
  'tr',
  implode('', [
    Html::tag(
      'th',
      Html::encode(
        match (true) {
          $event === '*' => sprintf('(%s)', Yii::t('app', 'Total')),
          $event === null => Yii::t('app-salmon-event3', '(Normal)'),
          $event instanceof SalmonEvent3 => Yii::t('app-salmon-event3', $event->name),
        },
      ),
      [
        'scope' => 'row',
        'class' => $event instanceof SalmonEvent3
          ? 'text-left text-start'
          : 'text-center',
      ],
    ),
    implode(
      '',
      array_map(
        fn (SalmonWaterLevel2 $tide): string => $this->render(
          'tide-cells',
          [
            'event' => $event,
            'stats' => $stats,
            'tide' => $tide,
            'tideJobs' => array_sum(
              array_map(
                fn (StatSalmon3TideEvent $model): int => $model->jobs,
                array_filter(
                  $stats,
                  fn (StatSalmon3TideEvent $model): bool => $model->tide_id === $tide->id,
                ),
              ),
            ),
            'totalJobs' => array_sum(
              array_map(
                fn (StatSalmon3TideEvent $model): int => $model->jobs,
                $stats,
              ),
            ),
          ],
        ),
        $tides,
      ),
    ),
  ]),
) . "\n" ?>

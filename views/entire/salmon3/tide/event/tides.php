<?php

/**
 * @copyright Copyright (C) 2022-2026 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\models\BigrunMap3;
use app\models\SalmonEvent3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3TideEvent;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var BigrunMap3|SalmonMap3 $map
 * @var View $this
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonWaterLevel2> $tides
 */

$statsList = StatSalmon3TideEvent::find()
  ->andWhere([
    'stage_id' => $map instanceof SalmonMap3 ? $map->id : null,
    'big_stage_id' => $map instanceof BigrunMap3 ? $map->id : null,
  ])
  ->orderBy([
    'tide_id' => SORT_ASC,
    'event_id' => SORT_ASC,
  ])
  ->all();

if (!$statsList) {
  return;
}

echo Html::tag(
  'div',
  implode(
    "\n",
    array_map(
      fn (SalmonWaterLevel2 $tide): string => $this->render('tide', [
        'events' => $events,
        'map' => $map,
        'tide' => $tide,
        'stats' => array_values(
          array_filter(
            $statsList,
            fn (StatSalmon3TideEvent $model): bool => $model->tide_id === $tide->id,
          ),
        ),
      ]),
      $tides,
    ),
  ),
  ['class' => 'row'],
) . "\n";

echo Html::tag(
  'div',
  Html::tag(
    'div',
    $this->render(
      'table',
      array_merge(
        compact('events', 'tides'),
        ['stats' => $statsList],
      ),
    ),
    ['class' => 'table-responsive table-responsive-force'],
  ),
  ['class' => 'mb-4'],
);

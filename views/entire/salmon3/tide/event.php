<?php

declare(strict_types=1);

use app\models\BigrunMap3;
use app\models\SalmonEvent3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var View $this
 * @var array<int, BigrunMap3 $bigMaps
 * @var array<int, SalmonEvent3> $events
 * @var array<int, SalmonMap3> $maps
 * @var array<int, SalmonWaterLevel2> $tides
 * @var array[] $mapTides
 */

echo Html::tag(
  'h2',
  Html::encode(Yii::t('app-salmon3', 'Known Occurrence')),
) . "\n";

foreach ($mapTides as $row) {
  /**
   * @var SalmonMap3|BigrunMap3|null $map
   */
  $map = match (true) {
    $row['big_stage_id'] !== null => $bigMaps[$row['big_stage_id']] ?? null,
    $row['stage_id'] !== null => $maps[$row['stage_id']] ?? null,
    default => null,
  };
  if ($map) {
    echo Html::tag(
      'div',
      implode('', [
        $this->render('event/heading', compact('map')),
        $this->render('event/tides', compact('events', 'map', 'tides')),
      ]),
      ['class' => 'mb-3'],
    ) . "\n";
  }
}

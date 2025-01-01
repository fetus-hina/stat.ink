<?php

/**
 * @copyright Copyright (C) 2022-2025 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@fetus.jp>
 */

declare(strict_types=1);

use app\assets\EntireSalmon3TideAsset;
use app\assets\RatioAsset;
use app\components\widgets\Icon;
use app\models\BigrunMap3;
use app\models\SalmonEvent3;
use app\models\SalmonMap3;
use app\models\SalmonWaterLevel2;
use app\models\StatSalmon3TideEvent;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

/**
 * @var BigrunMap3|SalmonMap3 $map
 * @var SalmonWaterLevel2 $tide
 * @var StatSalmon3TideEvent[] $stats
 * @var View $this
 * @var array<int, SalmonEvent3> $events
 */

EntireSalmon3TideAsset::register($this);
RatioAsset::register($this);

/**
 * @var array<string, int> $values
 */
$values = ArrayHelper::map(
  $stats,
  fn (StatSalmon3TideEvent $model): string => (string)(int)$model->event_id,
  fn (StatSalmon3TideEvent $model): int => $model->jobs,
);

/**
 * @var array<string, string> $labels
 */
$labels = array_map(
  fn (SalmonEvent3 $model): string => Yii::t('app-salmon-event3', $model->name),
  $events,
);
$labels['0'] = Yii::t('app-salmon-event3', '(Normal)');

$total = array_sum(array_values($values));

?>
<div class="col-xs-12 col-sm-6 col-md-4 col-lg-3 mb-3">
  <?= Html::tag(
    'h4',
    implode(' ', [
      Icon::s3SalmonTide($tide),
      Html::encode(Yii::t('app-salmon-tide2', $tide->name)),
    ]),
    ['class' => 'mt-0 mb-2'],
  ) . "\n" ?>
  <div class="mb-1 ratio ratio-1x1">
    <?= Html::tag(
      'div',
      '',
      [
        'class' => 'tide-event-pie-chart',
        'data' => [
          'labels' => Json::encode($labels),
          'values' => Json::encode($values),
        ],
      ],
    ) . "\n" ?>
  </div>
  <?= $this->render('../tide/n', ['n' => $total]) . "\n" ?>
</div>

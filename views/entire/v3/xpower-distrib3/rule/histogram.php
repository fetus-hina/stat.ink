<?php

declare(strict_types=1);

use app\components\helpers\XPowerNormalDistribution;
use app\models\StatXPowerDistrib3;
use app\models\StatXPowerDistribAbstract3;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use yii\web\View;

/**
 * @var StatXPowerDistrib3[] $data
 * @var StatXPowerDistribAbstract3|null $abstract
 * @var View $this
 */

if (!$data) {
  return;
}

$normalDistribData = Yii::$app->cache->getOrSet(
  [__FILE__, __LINE__, $abstract?->attributes],
  fn () => XPowerNormalDistribution::getDistributionFromStatXPowerDistribAbstract3(
    abstract: $abstract,
  ),
  86400,
);

?>
<div class="row">
  <div class="col-xs-12 col-md-9 col-lg-7 mb-3">
    <?= Html::tag('div', '', [
      'class' => 'ratio ratio-16x9 xpower-distrib-chart',
      'data' => [
        'translates' => Json::encode([
            'Normal Distribution' => Yii::t('app', 'Normal Distribution'),
            'Users' => Yii::t('app', 'Users'),
        ]),
        'dataset' => Json::encode(
          array_map(
            fn (StatXPowerDistrib3 $v): array => [
              'x' => (int)$v->x_power,
              'y' => (int)$v->users,
            ],
            $data,
          ),
        ),
        'normal-distribution' => Json::encode($normalDistribData),
      ],
    ]) . "\n" ?>
  </div>
</div>
